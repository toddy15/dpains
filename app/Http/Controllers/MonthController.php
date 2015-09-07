<?php

namespace App\Http\Controllers;

use App\Episode;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MonthController extends Controller
{
    /**
     * The year with the first data available.
     *
     * @var int
     */
    private $firstYear = 2015;

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
        // Ensure valid values for year and month
        $year = (int)$year;
        $month = (int)$month;
        // Do not show years before the database started and keep month between 1 and 12
        if (($year < $this->firstYear) or ($month < 1) or ($month > 12)) {
            abort(404);
        }
        // Convert to internal representation in the database (YYYY-MM)
        $formatted_month = sprintf("%4d-%02d", $year, $month);
        // Get all episodes valid in this month
        $episodes = $this->getPeopleForMonth($formatted_month);
        // Get all changes in this month
        $episode_changes = $this->getChangesForMonth($formatted_month);
        // Set up a readable month name
        $readable_month = Carbon::createFromDate($year, $month)->formatLocalized('%B %Y');
        // Generate the next and previous month urls
        if ($month == 12) {
            $next_month_url = url('month/' . sprintf("%4d/%02d", $year + 1, 1));
        } else {
            $next_month_url = url('month/' . sprintf("%4d/%02d", $year, $month + 1));
        }
        if ($month == 1) {
            if ($year == $this->firstYear) {
                $previous_month_url = '';
            } else {
                $previous_month_url = url('month/' . sprintf("%4d/%02d", $year - 1, 12));
            }
        } else {
            $previous_month_url = url('month/' . sprintf("%4d/%02d", $year, $month - 1));
        }
        return view('month.show', compact('episode_changes', 'episodes',
            'readable_month', 'next_month_url', 'previous_month_url'));
    }

    /**
     * Returns an array of people working in the given month.
     *
     * @param $month
     * @return mixed
     */
    private function getPeopleForMonth($month)
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

    private function getChangesForMonth($formatted_month)
    {
        return Episode::where('start_date', $formatted_month)
            ->leftJoin('staffgroups', 'staffgroup_id', '=', 'staffgroups.id')
            ->leftJoin('comments', 'comment_id', '=', 'comments.id')
            // First, order by staffgroups (weight parameter)
            ->orderBy('weight')
            // Second, order by name within the staffgroups
            ->orderBy('name')
            ->get();
    }
}
