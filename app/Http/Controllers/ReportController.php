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
        // Get the sorting key and direction from the request
        $sort_key = $request->get('sort');
        $direction = $request->get('direction');
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
        // Set up the staffgroups to get the correct sorting
        $staffgroup_names = Staffgroup::orderBy('weight')->lists('staffgroup')->toArray();
        foreach ($staffgroup_names as $staffgroup_name) {
            // Reduce staffgroups
            if ($staffgroup_name == 'FA' or $staffgroup_name == 'WB mit Nachtdienst') {
                $staffgroup_name = 'FA und WB mit Nachtdienst';
            }
            $staffgroups[$staffgroup_name] = [];
        }
        // To calculate the due shifts per month, cycle through
        // every month in the given year.
        for ($month = 1; $month <= 12; $month++) {
            // Set up a month usable for the database
            $formattedMonth = sprintf('%4d-%02d', $year, $month);
            // Get all employees for the current month
            $employees_in_month = Helper::getPeopleForMonth($formattedMonth);
            // Create a new array with the employee's id as
            // the array index.
            $employees = [];
            foreach ($employees_in_month as $employee) {
                $employees[$employee->employee_id] = $employee;
            }
            // Get all analyzed shifts for the current month
            $shifts = DB::table('analyzed_months')->where('month', $formattedMonth)->get();
            // Cycle through all people and add up the shifts
            foreach ($shifts as $shift) {
                // Determine the current person (for staffgroup etc.)
                $employee = $employees[$shift->employee_id];
                // Reduce staffgroups
                if ($employee->staffgroup == 'FA' or $employee->staffgroup == 'WB mit Nachtdienst') {
                    $employee->staffgroup = 'FA und WB mit Nachtdienst';
                }
                // Set up the result array, grouped by staffgroup
                if (!isset($staffgroups[$employee->staffgroup][$employee->employee_id])) {
                    $staffgroups[$employee->staffgroup][$employee->employee_id] = $this->newResultArray((array)$employee);
                }
                // Calculate the boni for vk and factors
                $person_bonus_night = 1 - ($employee->vk * $employee->factor_night);
                $person_bonus_nef = 1 - ($employee->vk * $employee->factor_nef);
                // Add up the shifts to the result array
                $staffgroups[$employee->staffgroup][$employee->employee_id]['planned_nights'] += $shift->nights;
                $staffgroups[$employee->staffgroup][$employee->employee_id]['planned_nefs'] += $shift->nefs;
                $staffgroups[$employee->staffgroup][$employee->employee_id]['bonus_planned_nights'][$month] = $person_bonus_night;
                $staffgroups[$employee->staffgroup][$employee->employee_id]['bonus_planned_nefs'][$month] = $person_bonus_nef;
                // Now add to the worked results, if the month has passed.
                if ($formattedMonth <= $worked_month) {
                    $staffgroups[$employee->staffgroup][$employee->employee_id]['worked_nights'] += $shift->nights;
                    $staffgroups[$employee->staffgroup][$employee->employee_id]['worked_nefs'] += $shift->nefs;
                }
            }
        }
        // Fill up the boni for each month that is not in the result array yet.
        $this->fillUpBoni($staffgroups);
        foreach ($staffgroups as $staffgroup => $employee) {
            // @TODO: Do not hardcode.
            switch ($staffgroup) {
                case 'LOA':
                    $due_nights = 12;
                    $due_nefs = 0;
                    break;
                case 'OA':
                    $due_nights = 44;
                    $due_nefs = 30;
                    break;
                case 'FA und WB mit Nachtdienst':
                    $due_nights = 55;
                    $due_nefs = 30;
                    break;
                default:
                    $due_nights = 0;
                    $due_nefs = 0;
            }
            // Finally, set up an array for the results table
            $rows = [];
            foreach ($employee as $person_number => $info) {
                // Calculate bonus nights and nefs by multiplying the
                // bonus VK with the average shifts per month.
                $bonus = $due_nights / 12 * $info['bonus_planned_nights'];
                $info['diff_planned_nights'] = (int)round($info['planned_nights'] + $bonus - $due_nights);
                $bonus = $due_nefs / 12 * $info['bonus_planned_nefs'];
                $info['diff_planned_nefs'] = (int)round($info['planned_nefs'] + $bonus - $due_nefs);
                // Use the sorting key as the array index, to enable the
                // sorting within the staffgroups.
                if (!array_key_exists($sort_key, $info)) {
                    $sort_key = 'name';
                }
                // If two values are the same, that information would get lost
                // by using only one index. Therefore, use a second index,
                // filling with the name. This way, the second sorting
                // after the first sorting will automatically use the name.
                $row_index = $info[$sort_key];
                $rows[$row_index][$info['name']] = (object)$info;
            }
            // Sort all staffgroups either asc or desc
            ($direction == 'desc') ? krsort($rows) : ksort($rows);
            // Now sort by name
            foreach ($rows as $index => $data) {
                ksort($rows[$index]);
            }
            // Do not show empty staffgroups
            if (count($rows)) {
                // Add to tables
                $tables[$staffgroup] = $rows;
            }
        }
        return view('reports.show_year', compact('year',
            'readable_planned_month', 'readable_worked_month', 'tables'));
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

    private function newResultArray($person)
    {
        return array_merge($person, [
            'worked_nights' => 0,
            'planned_nights' => 0,
            'worked_nefs' => 0,
            'planned_nefs' => 0,
        ]);
    }

    private function fillUpBoni(&$staffgroups)
    {
        foreach ($staffgroups as $staffgroup => $person) {
            foreach ($person as $person_number => $info) {
                // Calculate the months with no data yet.
                $missing_months = 12 - count($info['bonus_planned_nights']);
                // Sum up the bonus for all months with data.
                $bonus = array_sum($info['bonus_planned_nights']);
                // The total bonus is the sum of all data plus 1 for each month
                // without data.
                $staffgroups[$staffgroup][$person_number]['bonus_planned_nights'] = $bonus + $missing_months;
                // Now do the same three steps for the NEF bonus counter.
                $missing_months = 12 - count($info['bonus_planned_nefs']);
                $bonus = array_sum($info['bonus_planned_nefs']);
                $staffgroups[$staffgroup][$person_number]['bonus_planned_nefs'] = $bonus + $missing_months;
            }
        }
    }
}
