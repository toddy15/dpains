<?php

namespace App\Http\Controllers;

use App\Dpains\Helper;
use App\Dpains\Planparser;
use App\Rawplan;
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
        // Create an array with a mapping of person_number -> shifts
        $shifts = [];
        foreach ($reports as $report) {
            $shifts[$report->number] = $report;
        }
        // In order to use the grouping by staffgroups, it it necessary
        // to set up an new array of names and counted shifts. The
        // array $names is already sorted correctly with staffgroups.
        foreach ($names as $person_number => $name) {
            $results[] = (object)[
                'name' => $name,
                'shifts' => $shifts[$person_number],
            ];
        }
        return view('reports.show_month', compact('results',
            'readable_month', 'next_month_url', 'previous_month_url'));
    }

    public function showYear($year)
    {
        // Determine which month has been planned and which
        // has been worked already.
        $planned_month = $this->getPlannedMonth($year);
        if (!$planned_month) {
            // There is no data at all, so abort.
            abort(404);
        }
        $worked_month = $this->getWorkedMonth($year);
        dd($worked_month);
        // To calculate the due shifts per month, cycle through
        // every month in the given year.
        for ($month = 1; $month <= 12; $month++) {
            $formattedMonth = sprintf('%4d-%02d', $year, $month);
        }
        return $formattedMonth;
    }

    public function analyzeAll()
    {
        // Get all months with raw data
        $months = Rawplan::lists('month');
        foreach ($months as $month) {
            $planparser = new Planparser($month);
            $planparser->storeShiftsForPeople();
        }
    }

    /**
     * Returns the highest month in the given year. This might be
     * in the planned status.
     *
     * @param int $year
     * @return string|null Formatted month (YYYY-MM)
     */
    private function getPlannedMonth($year)
    {
        return Rawplan::where('month', 'like', "$year%")->max('month');
    }

    /**
     * Returns the month which has been updated after the month
     * has passed. This ensures that the actually worked shifts
     * are recognized.
     *
     * @param int $year
     * @return string|null Formatted month (YYYY-MM)
     */
    private function getWorkedMonth($year)
    {
        return Rawplan::where('month', 'like', "$year%")
            ->whereRaw('left(updated_at, 7) > month')->max('month');
    }
}
