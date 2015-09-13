<?php

namespace App\Dpains;

use App\Episode;
use Illuminate\Support\Facades\DB;

class Helper
{
    /**
     * The year with the first data available.
     *
     * @var int
     */
    public static $firstYear = 2015;

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
     * Return an array of people's names in the given month.
     * The array keys are the people's unique number.
     *
     * @param $month
     * @return array
     */
    public static function getNamesForMonth($month)
    {
        $people = Helper::getPeopleForMonth($month);
        $names = [];
        foreach ($people as $person) {
            $names[$person->number] = $person->name;
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
                    ->whereRaw('`e1`.`number` = `e2`.`number`')
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
}