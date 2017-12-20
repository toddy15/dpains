<?php

namespace App\Dpains;

use App\DueShift;
use App\Episode;
use App\Rawplan;
use App\Staffgroup;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class Helper
{
    /**
     * The year with the first data available.
     *
     * @var int
     */
    public static $firstYear = 2016;

    /**
     * Validates the given year and month, returning a formatted
     * representation. If the date is not valid, the app will abort
     * with a HTTP 404 error.
     *
     * @param $year
     * @param $month
     * @return string
     */
    public static function validateAndFormatDate($year, $month)
    {
        // Ensure a valid date and return in a format usable for database queries.
        $year = (int)$year;
        $month = (int)$month;
        // Do not show years before the database started and keep month between 1 and 12
        if (($year < Helper::$firstYear) or ($month < 1) or ($month > 12)) {
            abort(404);
        }
        // Convert to internal representation in the database (YYYY-MM)
        return sprintf("%4d-%02d", $year, $month);
    }

    /**
     * Generate a url for the next month.
     *
     * @param $prefix
     * @param $year
     * @param $month
     * @return string
     */
    public static function getNextMonthUrl($prefix, $year, $month)
    {
        if ($month == 12) {
            $year++;
            $month = 1;
        } else {
            $month++;
        }
        return url($prefix . sprintf('%4d/%02d', $year, $month));
    }

    /**
     * Generate a url for the previous month. If there is no previous
     * month, returns an empty string.
     *
     * @param $prefix
     * @param $year
     * @param $month
     * @return string
     */
    public static function getPreviousMonthUrl($prefix, $year, $month)
    {
        $url = '';
        if ($month == 1) {
            $year--;
            $month = 12;
        } else {
            $month--;
        }
        if ($year < Helper::$firstYear) {
            return '';
        }
        else {
            return url($prefix . sprintf('%4d/%02d', $year, $month));
        }
    }

    /**
     * Generate an URL for the next year.
     *
     * @param $prefix
     * @param $year
     * @return string
     */
    public static function getNextYearUrl($prefix, $year)
    {
        $year++;
        return url($prefix . sprintf('%4d/', $year));
    }

    /**
     * Generate an URL for the previous year.
     *
     * @param $prefix
     * @param $year
     * @return string
     */
    public static function getPreviousYearUrl($prefix, $year)
    {
        $year--;
        if ($year < Helper::$firstYear) {
            return '';
        }
        else {
            return url($prefix . sprintf('%4d/', $year));
        }
    }

    /**
     * Return an array of people's names in the given month.
     * The array keys are the people's unique number.
     *
     * @param $month
     * @return array
     */
    public static function getNamesForMonth($month)
    {
        $employees = Helper::getPeopleForMonth($month);
        $names = [];
        foreach ($employees as $employee) {
            $names[$employee->employee_id] = $employee->name;
        }
        return $names;
    }

    /**
     * Returns an array of people working in the given month.
     *
     * @param $month
     * @return mixed
     */
    public static function getPeopleForMonth($month)
    {
        return DB::table('episodes as e1')
            ->leftJoin('staffgroups', 'e1.staffgroup_id', '=', 'staffgroups.id')
            ->leftJoin('comments', 'e1.comment_id', '=', 'comments.id')
            // With this complicated subquery we get the row with the
            // current data for the specified month.
            ->where('e1.start_date', function ($query) use ($month) {
                $query->selectRaw('MAX(`e2`.`start_date`)')
                    ->from('episodes as e2')
                    ->whereRaw('`e1`.`employee_id` = `e2`.`employee_id`')
                    ->where('e2.start_date', '<=', $month);
            })
            // This filters out the episodes with "Vertragsende".
            // In order to get episodes without a comment (= NULL)
            // as well, we need to include those comments explicitely.
            ->where(function ($query) {
                $query->where('comment', 'not like', 'Vertragsende')
                    ->orWhereNull('comment');
            })
            // First, order by staffgroups (weight parameter)
            ->orderBy('weight')
            // Second, order by name within the staffgroups
            ->orderBy('name')
            ->get();
    }

    /**
     * Returns all people with changes in the given month.
     *
     * @param $month
     * @return mixed
     */
    public static function getChangesForMonth($month)
    {
        return Episode::where('start_date', $month)
            ->leftJoin('staffgroups', 'staffgroup_id', '=', 'staffgroups.id')
            ->leftJoin('comments', 'comment_id', '=', 'comments.id')
            // First, order by staffgroups (weight parameter)
            ->orderBy('weight')
            // Second, order by name within the staffgroups
            ->orderBy('name')
            ->get();
    }

    public static function getTablesForYear(HttpRequest $request, $year, $worked_month, $non_anon_employee_id = 0)
    {
        $tables = [];
        // Get the sorting key and direction from the request
        $sort_key = $request->get('sort');
        $direction = $request->get('direction');
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
            // If this is anonymous access, determine if the current month
            // should be included.
            if ($non_anon_employee_id) {
                $include_in_anon_report = Rawplan::where('month', $formattedMonth)->value('anon_report');
                if (!$include_in_anon_report) continue;
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
                    $staffgroups[$employee->staffgroup][$employee->employee_id] = Helper::newResultArray((array)$employee);
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
        Helper::fillUpBoni($staffgroups);
        foreach ($staffgroups as $staffgroup => $employee) {
            $due_nights = Helper::getDueNightShifts($staffgroup, $year);
            $due_nefs = Helper::getDueNefShifts($staffgroup, $year);
            // Finally, set up an array for the results table
            $rows = [];
            // Determine if the table is for anonymous access.
            // If so, only show the name of the non-anonymous employee id
            // and remove staffgroups he/she is not part of.
            $include_staffgroup_in_tables = true;
            if ($non_anon_employee_id) {
                $include_staffgroup_in_tables = false;
            }
            foreach ($employee as $employee_id => $info) {
                // Calculate bonus nights and nefs by multiplying the
                // bonus VK with the average shifts per month.
                $bonus = $due_nights / 12 * $info['bonus_planned_nights'];
                $info['diff_planned_nights'] = (int)round($info['planned_nights'] + $bonus - $due_nights);
                $bonus = $due_nefs / 12 * $info['bonus_planned_nefs'];
                $info['diff_planned_nefs'] = (int)round($info['planned_nefs'] + $bonus - $due_nefs);
                // Use the sorting key as the array index, to enable the
                // sorting within the staffgroups.
                // If this is for anonymous access, use diff_planned_nights instead of name.
                if (!array_key_exists($sort_key, $info)) {
                    if ($non_anon_employee_id) {
                        $sort_key = 'diff_planned_nights';
                    }
                    else {
                        $sort_key = 'name';
                    }
                }
                // Is the employee part of this staffgroup?
                if ($non_anon_employee_id) {
                    if ($employee_id == $non_anon_employee_id) {
                        $include_staffgroup_in_tables = true;
                        $info['highlight_row'] = true;
                    }
                    else {
                        // Anonymize the information of other employees
                        // Use the underscore as first character to always
                        // sort the employee's name above the random names.
                        $info['name'] = '_' . str_random();
                        $info['worked_nights'] = 0;
                        $info['planned_nights'] = 0;
                        $info['worked_nefs'] = 0;
                        $info['planned_nefs'] = 0;
                    }
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
            if (count($rows) and $include_staffgroup_in_tables) {
                // Add to tables
                $tables[$staffgroup] = [
                    'rows' => $rows,
                    'due_nights' => $due_nights,
                    'due_nefs' => $due_nefs,
                ];
            }
        }
        return $tables;
    }

    public static function newResultArray($person)
    {
        return array_merge($person, [
            'worked_nights' => 0,
            'planned_nights' => 0,
            'worked_nefs' => 0,
            'planned_nefs' => 0,
        ]);
    }

    public static function fillUpBoni(&$staffgroups)
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

    public static function sortTableBy($column, $body, $year, $hash='')
    {
        // Provide default values, if the parameters are not set
        $currentColumn = Request::get('sort') ?: 'name';
        $currentDirection = Request::get('direction') ?: 'asc';
        // If the hash is given, use anonymous access and another default
        if ($hash) {
            $currentColumn = Request::get('sort') ?: 'diff_planned_nights';
        }
        // Flip direction if clicked on same header
        $direction = ($currentDirection == 'asc') ? 'desc' : 'asc';
        // Always use ascending direction if a new column is selected
        if ($currentColumn != $column) {
            $direction = 'asc';
        }
        // Create link
        $link = link_to_action('ReportController@showYear', $body,
            ['year' => $year, 'sort' => $column, 'direction' => $direction]);
        if ($hash) {
            $link = link_to_action('AnonController@showYear', $body,
                ['year' => $year, 'hash' => $hash, 'sort' => $column, 'direction' => $direction]);
        }
        // Append arrows to the current sorted column
        if ($column == $currentColumn) {
            if ($currentDirection == 'asc') {
                $link .= '<span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span>';
            }
            else {
                $link .= '<span class="glyphicon glyphicon-sort-by-attributes-alt" aria-hidden="true"></span>';
            }
        }
        return $link;
    }

    /**
     * Returns the highest year which has been planned.
     *
     * @return string|null Formatted year (YYYY)
     */
    public static function getPlannedYear()
    {
        $month =  Rawplan::all()->max('month');
        return substr($month, 0, 4);
    }

    /**
     * Returns the highest month in the given year. This might be
     * in the planned status.
     *
     * @param int $year
     * @return string|null Formatted month (YYYY-MM)
     */
    public static function getPlannedMonth($year)
    {
        return Rawplan::where('month', 'like', "$year%")->max('month');
    }

    /**
     * Returns the month which has been updated after the month
     * has passed. This ensures that the actually worked shifts
     * are recognized.
     *
     * If the year is null, returns the latest month.
     *
     * @param int $year
     * @return string|null Formatted month (YYYY-MM)
     */
    public static function getWorkedMonth($year=null)
    {
        if ($year) {
            return Rawplan::where('month', 'like', "$year%")
                ->whereRaw('left(updated_at, 7) > month')->max('month');
        }
        else {
            return Rawplan::whereRaw('left(updated_at, 7) > month')->max('month');
        }
    }

    /**
     * Returns the highest month in the given year for anonymous access.
     * This might be in the planned status.
     *
     * @param int $year
     * @return string|null Formatted month (YYYY-MM)
     */
    public static function getPlannedMonthForAnonAccess($year)
    {
        return Rawplan::where('month', 'like', "$year%")
            ->where('anon_report', true)
            ->max('month');
    }

    /**
     * Sum up the VK for the given year, grouped by the staffgroup.
     * With $which_vk, specify the VK calculation: all, nef, night
     *
     * @param string $which_vk
     * @param $year
     * @param $staffgroups
     * @param $vk_per_month
     */
    public static function sumUpVKForYear($which_vk, $year, &$staffgroups, &$vk_per_month)
    {
        // Set up temporary result arrays
        $months = [];
        $employee_info = [];
        // Initialize the counter for all VK
        $vk_per_month['all'] = array_fill(1, 12, 0);
        // Initialize the counter for yearly mean of all VK
        $vk_per_month['all']['yearly_mean'] = 0;
        for ($month = 1; $month <= 12; $month++) {
            $formatted_month = Helper::validateAndFormatDate($year, $month);
            // Get all episodes valid in this month
            $episodes = Helper::getPeopleForMonth($formatted_month);
            foreach ($episodes as $episode) {
                // Reduce staffgroups
                if ($episode->staffgroup == 'FA' or $episode->staffgroup == 'WB mit Nachtdienst') {
                    $episode->staffgroup = 'FA und WB mit Nachtdienst';
                }
                // Fill the VK sum array from 1 to 12 with 0, if not set
                if (!isset($vk_per_month[$episode->staffgroup])) {
                    $vk_per_month[$episode->staffgroup] = array_fill(1, 12, 0);
                    // Initialize the counter for yearly mean of staffgroup VK
                    $vk_per_month[$episode->staffgroup]['yearly_mean'] = 0;
                }
                // Initialize a month array, if not set
                if (!isset($months[$episode->staffgroup][$episode->employee_id])) {
                    $months[$episode->staffgroup][$episode->employee_id] = array_fill(1, 12, [
                        'vk' => '&ndash;',
                        'changed' => false,
                    ]);
                }
                // Always use the last available name,
                // overwrite previous information.
                $employee_info[$episode->staffgroup][$episode->employee_id] = [
                    'name' => $episode->name
                ];
                // Store the VK for the current month
                $vk = $episode->vk;
                switch ($which_vk) {
                    case 'night':
                        $vk = $episode->vk * $episode->factor_night;
                        break;
                    case 'nef':
                        $vk = $episode->vk * $episode->factor_nef;
                        break;
                }
                // Ensure a nicely formatted VK
                $vk = sprintf('%.3f', round($vk, 3));
                $months[$episode->staffgroup][$episode->employee_id][$month]['vk'] = $vk;
                // Mark changes
                if ($month > 1) {
                    if ($months[$episode->staffgroup][$episode->employee_id][$month - 1]['vk'] != $vk) {
                        $months[$episode->staffgroup][$episode->employee_id][$month]['changed'] = true;
                    }
                }
                // Sum up for the month
                $vk_per_month[$episode->staffgroup][$month] += (float) $vk;
                // Sum up for the grand total per month
                $vk_per_month['all'][$month] += (float) $vk;
                // Sum up for the mean vk per year
                $vk_per_month[$episode->staffgroup]['yearly_mean'] += (float) $vk;
                $vk_per_month['all']['yearly_mean'] += (float) $vk;
            }
        }
        // Format the 'all' counter nicely
        foreach ($vk_per_month as $staffgroup => $sums) {
            for ($month = 1; $month <= 12; $month++) {
                $vk_per_month[$staffgroup][$month] = sprintf('%.3f', $vk_per_month[$staffgroup][$month]);
            }
            // Calculate the yearly mean vk per staffgroup
            $vk_per_month[$staffgroup]['yearly_mean'] = sprintf('%.3f', $vk_per_month[$staffgroup]['yearly_mean'] / 12);
        }
        // Merge the final array for display
        foreach ($employee_info as $staffgroup => $info) {
            foreach ($info as $employee_id => $employee) {
                // Make sort key for array
                $sort_key = $employee['name'];
                $staffgroups[$staffgroup][$sort_key] = [
                    'name' => $employee['name'],
                    'months' => $months[$staffgroup][$employee_id],
                ];
            }
            // Sort the array by sorting keys
            ksort($staffgroups[$staffgroup], SORT_NATURAL);
        }
    }

    private static function getDueShifts($staffgroup, $year)
    {
        // Special case for combined staffgroup
        if ($staffgroup == 'FA und WB mit Nachtdienst') {
            $staffgroup = 'FA';
        }
        $staffgroup = Staffgroup::where('staffgroup', $staffgroup)->first();
        return DueShift::where('year', $year)
            ->where('staffgroup_id', $staffgroup->id)->first();
    }

    public static function getDueNightShifts($staffgroup, $year)
    {
        $due_shift = self::getDueShifts($staffgroup, $year);
        if ($due_shift) {
            return $due_shift->nights;
        }
        return 0;
    }

    public static function getDueNefShifts($staffgroup, $year)
    {
        $due_shift = self::getDueShifts($staffgroup, $year);
        if ($due_shift) {
            return $due_shift->nefs;
        }
        return 0;
    }
}
