<?php

namespace App\Dpains;

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
}