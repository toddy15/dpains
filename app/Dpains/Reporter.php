<?php

namespace App\Dpains;

use App\Episode;
use Illuminate\Support\Facades\DB;

class Reporter
{
    /**
     * Return an array of people's names in the given month.
     * The array keys are the people's unique number.
     *
     * @param $month
     * @return array
     */
    public function getNamesForMonth($month) {
        $people = $this->getPeopleForMonth($month);
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
    public function getPeopleForMonth($month)
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
    public function getChangesForMonth($month)
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
