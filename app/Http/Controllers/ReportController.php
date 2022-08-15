<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Rawplan;
use App\Services\Helper;
use App\Services\Planparser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function showMonth($year, $month): View
    {
        $results = [];
        $formatted_month = Helper::validateAndFormatDate($year, $month);
        // Set up a readable month name
        $readable_month = Carbon::createFromDate($year, $month)
            ->locale('de')
            ->isoFormat('MMMM YYYY');
        // Generate the next and previous month urls
        $next_month_url = Helper::getNextMonthUrl('report/', $year, $month);
        $previous_month_url = Helper::getPreviousMonthUrl(
            'report/',
            $year,
            $month,
        );
        // Get the names for this month
        $names = Helper::getNamesForMonth($formatted_month);
        // Get information for all people in this month
        $reports = DB::table('analyzed_months')
            ->where('month', $formatted_month)
            ->get();
        // If there is no data yet, abort here.
        if ($reports->isEmpty()) {
            return view(
                'reports.show_month',
                compact(
                    'results',
                    'readable_month',
                    'next_month_url',
                    'previous_month_url',
                ),
            );
        }
        // Create an array with a mapping of employee_id -> shifts
        $shifts = [];
        foreach ($reports as $report) {
            $shifts[$report->employee_id] = $report;
        }
        // In order to use the grouping by staffgroups, it is necessary
        // to set up a new array of names and counted shifts. The
        // array $names is already sorted correctly with staffgroups.
        foreach ($names as $employee_id => $name) {
            $results[] = (object) [
                'name' => $name,
                'shifts' => $shifts[$employee_id],
            ];
        }

        return view(
            'reports.show_month',
            compact(
                'results',
                'readable_month',
                'next_month_url',
                'previous_month_url',
            ),
        );
    }

    public function showYear(Request $request, $year): View
    {
        // Determine which month has been planned
        $planned_month = Helper::getPlannedMonth($year);
        if (! $planned_month) {
            // There is no data at all, so abort.
            abort(404);
        }
        // Determine which month is in the past and therefore
        // represents the actually worked shifts.
        $worked_month = Helper::getWorkedMonth($year);
        // Set up readable month names
        $readable_planned_month = Carbon::parse($planned_month)
            ->locale('de')
            ->isoFormat('MMMM YYYY');
        $readable_worked_month = '';
        if (! empty($worked_month)) {
            $readable_worked_month = Carbon::parse($worked_month)
                ->locale('de')
                ->isoFormat('MMMM YYYY');
        }
        // Get the date and time of latest change
        $latest_change = Rawplan::latest('updated_at')
            ->where('month', 'LIKE', "$year%")
            ->value('updated_at');
        $latest_change = Carbon::parse($latest_change)
            ->locale('de')
            ->isoFormat('Do MMMM YYYY, HH:mm');
        // Generate the next and previous year urls
        $previous_year_url = Helper::getPreviousYearUrl('report/', $year);
        $next_year_url = Helper::getNextYearUrl('report/', $year);
        $tables = Helper::getTablesForYear($request, $year, $worked_month);

        return view(
            'reports.show_year',
            compact(
                'year',
                'previous_year_url',
                'next_year_url',
                'latest_change',
                'readable_planned_month',
                'readable_worked_month',
                'tables',
            ),
        );
    }

    public function showBuAndCon(Request $request, $year): View
    {
        $all_bu_and_con = [];
        // Get all employees with bu and con in the last, current, and next year
        for (
            $current_year = $year - 1;
            $current_year <= $year + 1;
            $current_year++
        ) {
            $all_bu_and_con[$current_year] = DB::table('analyzed_months')
                ->where('month', 'LIKE', "$current_year%")
                ->where(function ($query) {
                    $query->where('bus', '>', 0)->orWhere('cons', '>', 0);
                })
                ->get();
        }
        // This is sorted by year, then months with employee ids.
        // Create a new array with employee id as key, then year, then bu and con.
        $employees = [];
        foreach ($all_bu_and_con as $current_year => $months) {
            // $months contains the month as key, then employee id and bu/con.
            foreach ($months as $month => $data) {
                // Initialize the result array
                if (! isset($employees[$data->employee_id])) {
                    $e = Employee::findOrFail($data->employee_id);
                    $bu_cleartext = 'Nicht hinterlegt';
                    if ($e->bu_start == 'even') {
                        $bu_cleartext = 'Gerades Jahr';
                    } elseif ($e->bu_start == 'odd') {
                        $bu_cleartext = 'Ungerades Jahr';
                    }
                    $employees[$data->employee_id] = [
                        'name' => $e->name,
                        'bu_cleartext' => $bu_cleartext,
                        'data' => [
                            $year - 1 => ['bus' => 0, 'cons' => 0],
                            $year => ['bus' => 0, 'cons' => 0],
                            $year + 1 => ['bus' => 0, 'cons' => 0],
                        ],
                    ];
                }
                // Sum up bus and cons for the given year
                $employees[$data->employee_id]['data'][$current_year]['bus'] +=
                    $data->bus;
                $employees[$data->employee_id]['data'][$current_year]['cons'] +=
                    $data->cons;
            }
        }
        // Remove the previous or next year, depending on the start of BU
        // and sum up the total
        foreach ($employees as $id => $employee) {
            $bu_cleartext = $employee['bu_cleartext'];
            if ($year % 2) {
                // Year is odd
                if ($bu_cleartext == 'Gerades Jahr') {
                    // If the BU start is even, it's last and this year.
                    // Unset next.
                    $employees[$id]['data'][$year + 1]['bus'] = '–';
                    $employees[$id]['data'][$year + 1]['cons'] = '–';
                } elseif ($bu_cleartext == 'Ungerades Jahr') {
                    // Otherwise, it's this and next year.
                    // Unset previous.
                    $employees[$id]['data'][$year - 1]['bus'] = '–';
                    $employees[$id]['data'][$year - 1]['cons'] = '–';
                }
            } else {
                // Year is even
                if ($bu_cleartext == 'Gerades Jahr') {
                    // If the BU start is even, it's this and next year.
                    // Unset previous.
                    $employees[$id]['data'][$year - 1]['bus'] = '–';
                    $employees[$id]['data'][$year - 1]['cons'] = '–';
                } elseif ($bu_cleartext == 'Ungerades Jahr') {
                    // Otherwise, it's last and this year.
                    // Unset next.
                    $employees[$id]['data'][$year + 1]['bus'] = '–';
                    $employees[$id]['data'][$year + 1]['cons'] = '–';
                }
            }
            // Sum up the total.
            $employees[$id]['sum'] = 0;
            foreach ($employees[$id]['data'] as $buandcon) {
                $employees[$id]['sum'] +=
                    (int) $buandcon['bus'] + (int) $buandcon['cons'];
            }
        }
        // Sort by name
        uasort($employees, fn ($a, $b) => $a['name'] <=> $b['name']);
        $previous_year_url = Helper::getPreviousYearUrl(
            'report/buandcon/',
            $year,
        );
        $next_year_url = Helper::getNextYearUrl('report/buandcon/', $year);

        return view(
            'reports.show_bu_and_con',
            compact('year', 'previous_year_url', 'next_year_url', 'employees'),
        );
    }

    public function refresh(Request $request): View
    {
        // Determine the highest month with data.
        $highest_month = Helper::getPlannedMonth(
            Carbon::now()->addYear()->yearIso,
        );
        // If the next year does not have data, this will return NULL.
        if (! $highest_month) {
            $highest_month = Helper::getPlannedMonth(Carbon::now()->yearIso);
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
            $recalculation_months[] = sprintf(
                '%04d-%02d',
                $highest_year,
                $month,
            );
        }
        // Finally, cycle through all recalculation months and check if
        // everything can be parsed without errors
        $error_messages = [];
        foreach ($recalculation_months as $month) {
            $rawplan = Rawplan::where('month', $month)->first();
            $planparser = new Planparser(
                $month,
                $rawplan->people,
                $rawplan->shifts,
            );
            $error_messages[] = $planparser->validatePeople();
            $error_messages[] = $planparser->validateShifts();
        }
        // Make a flat collection from the error_messages array
        $errors = collect($error_messages)->flatten();
        // Only store the new calculation if there are no errors
        if (! $errors->count()) {
            foreach ($recalculation_months as $month) {
                $rawplan = Rawplan::where('month', $month)->first();
                $planparser = new Planparser(
                    $month,
                    $rawplan->people,
                    $rawplan->shifts,
                );
                $planparser->storeShiftsForPeople();
            }
            $request
                ->session()
                ->flash('info', 'Alle Monate wurden neu berechnet.');
        }

        return view('reports.refresh')->withErrors($errors->toArray());
    }
}
