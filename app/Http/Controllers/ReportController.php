<?php

namespace App\Http\Controllers;

use App\Dpains\Helper;
use App\Dpains\Planparser;
use App\Rawplan;
use App\Staffgroup;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function showMonth($year, $month)
    {
        $results = [];
        $formatted_month = Helper::validateAndFormatDate($year, $month);
        // Set up a readable month name
        $readable_month = Carbon::createFromDate($year, $month)->formatLocalized('%B %Y');
        // Generate the next and previous month urls
        $next_month_url = Helper::getNextMonthUrl('report/', $year, $month);
        $previous_month_url = Helper::getPreviousMonthUrl('report/', $year, $month);
        // Get the names for this month
        $names = Helper::getNamesForMonth($formatted_month);
        // Get information for all people in this month
        $reports = DB::table('analyzed_months')->where('month', $formatted_month)->get();
        // If there is no data yet, abort here.
        if (empty($reports)) {
            return view('reports.show_month', compact('results',
                'readable_month', 'next_month_url', 'previous_month_url'));
        }
        // Create an array with a mapping of employee_id -> shifts
        $shifts = [];
        foreach ($reports as $report) {
            $shifts[$report->employee_id] = $report;
        }
        // In order to use the grouping by staffgroups, it it necessary
        // to set up an new array of names and counted shifts. The
        // array $names is already sorted correctly with staffgroups.
        foreach ($names as $employee_id => $name) {
            $results[] = (object)[
                'name' => $name,
                'shifts' => $shifts[$employee_id],
            ];
        }
        return view('reports.show_month', compact('results',
            'readable_month', 'next_month_url', 'previous_month_url'));
    }

    public function showYear(Request $request, $year)
    {
        // Determine which month has been planned
        $planned_month = Helper::getPlannedMonth($year);
        if (!$planned_month) {
            // There is no data at all, so abort.
            abort(404);
        }
        // Determine which month is in the past and therefore
        // represents the actually worked shifts.
        $worked_month = Helper::getWorkedMonth($year);
        // Set up readable month names
        $readable_planned_month = Carbon::parse($planned_month)->formatLocalized('%B %Y');
        $readable_worked_month = '';
        if (!empty($worked_month)) {
            $readable_worked_month = Carbon::parse($worked_month)->formatLocalized('%B %Y');
        }
        // Get the date and time of latest change
        $latest_change = Rawplan::orderBy('updated_at', 'desc')->value('updated_at');
        $latest_change = Carbon::parse($latest_change)->formatLocalized('%e. %B %Y, %H:%M');
        $tables = Helper::getTablesForYear($request, $year, $worked_month);
        return view('reports.show_year', compact('year',
            'latest_change', 'readable_planned_month', 'readable_worked_month', 'tables'));
    }

    public function refresh(Request $request)
    {
        // Determine the highest month with data.
        $highest_month = Helper::getPlannedMonth(date('Y') + 1);
        // If the next year does not have data, this will return NULL.
        if (!$highest_month) {
            $highest_month = Helper::getPlannedMonth(date('Y'));
        }
        // Set up result array
        $recalculation_months = [];
        // Now highest_month will be in the form YYYY-MM.
        // Cycle through all 12 months from beginning to highest_year - 1.
        $highest_year = substr($highest_month, 0, 4);
        $highest_month = substr($highest_month, 5, 2);
        for ($year = Helper::$firstYear; $year < $highest_year; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                $recalculation_months[] = sprintf('%04d-%02d', $year, $month);
            }
        }
        // Cycle though all months up to current planned month in the highest year.
        for ($month = 1; $month <= $highest_month; $month++) {
            $recalculation_months[] = sprintf('%04d-%02d', $highest_year, $month);
        }
        // Finally, cycle through all recalculation months and check if
        // everything can be parsed without errors
        $error_messages = [];
        foreach ($recalculation_months as $month) {
            $planparser = new Planparser($month);
            $error_messages[] = $planparser->validatePeople();
            $error_messages[] = $planparser->validateShifts();
        }
        // Make a flat collection from the error_messages array
        $errors = collect($error_messages)->flatten();
        // Only store the new calculation if there are no errors
        if (!$errors->count()) {
            foreach ($recalculation_months as $month) {
                $planparser = new Planparser($month);
                $planparser->storeShiftsForPeople();
            }
            $request->session()->flash('info', 'Alle Monate wurden neu berechnet.');
        }
        return view('reports.refresh')->withErrors($errors);
    }
}
