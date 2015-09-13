<?php

namespace App\Http\Controllers;

use App\Dpains\Helper;
use App\Dpains\Reporter;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MonthController extends Controller
{
    /**
     * Show the people working in the given month with their
     * calculated night shifts and nef shifts.
     *
     * @param $year
     * @param $month
     * @return mixed
     */
    public function show($year, $month)
    {
        $reporter = new Reporter();
        $formatted_month = Helper::validateAndFormatDate($year, $month);
        // Get all episodes valid in this month
        $episodes = $reporter->getPeopleForMonth($formatted_month);
        // Get all changes in this month
        $episode_changes = $reporter->getChangesForMonth($formatted_month);
        // Set up a readable month name
        $readable_month = Carbon::createFromDate($year, $month)->formatLocalized('%B %Y');
        // Generate the next and previous month urls
        $next_month_url = Helper::getNextMonthUrl('month/', $year, $month);
        $previous_month_url = Helper::getPreviousMonthUrl('month/', $year, $month);
        return view('month.show', compact('episode_changes', 'episodes',
            'readable_month', 'next_month_url', 'previous_month_url'));
    }
}
