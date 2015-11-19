<?php

namespace App\Http\Controllers;

use App\Dpains\Helper;
use App\Employee;
use App\Episode;
use App\Comment;
use App\PersonInfo;
use App\Staffgroup;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class EpisodeController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        // See if there is a valid employee
        $employee_id = (int)$request->get('employee_id');
        // Is there already an episode for this employee?
        // If yes, retrieve the latest episode for the default values.
        $episode = Episode::where('employee_id', $employee_id)
            ->orderBy('start_date', 'desc')->first();
        if (!$episode) {
            // There are no episodes, so create a new employee
            // using sane default values.
            $episode = new Episode();
            $episode->start_date = date("Y-m");
            $episode->vk = "1.000";
            $episode->factor_night = "0.000";
            $episode->factor_nef = "0.000";
        }
        // Get the comments for the select box
        $comments = Comment::all()->lists('comment', 'id')->toArray();
        // Add an empty comment
        $comments[0] = '--';
        // Sort by comment, maintaining the index association
        asort($comments);
        // Get the staffgroups for the select box
        $staffgroups = Staffgroup::all()->sortBy('weight')
            ->lists('staffgroup', 'id')->toArray();
        // Allow from the beginning of database storage or some years back
        $start_year = max(Helper::$firstYear, date('Y') - 3);
        // ... to some years ahead
        $end_year = date('Y') + 3;
        // Turn the start_date field into year and month for the form
        list($episode->year, $episode->month) = explode('-', $episode->start_date);
        return view('episodes.create', compact(
            'episode', 'comments', 'staffgroups', 'start_year', 'end_year'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'month' => 'required',
            'year' => 'required',
            'vk' => 'required|numeric|between:0,1',
            'factor_night' => 'required|numeric|between:0,2',
            'factor_nef' => 'required|numeric|between:0,2',
        ]);
        // Set the month to the formatted string for database storage.
        $start_date = Helper::validateAndFormatDate($request->get('year'), $request->get('month'));
        // Set the start_date to the database format YYYY-MM.
        $request->merge(['start_date' => $start_date]);
        // Check if the episode is for a new employee
        $episode = $request->all();
        if ($episode['employee_id'] == 0) {
            // This is a new employee, so create a new entry.
            $employee = Employee::create(['email' => str_random(), 'hash' => str_random()]);
            $episode['employee_id'] = $employee->id;
        }
        Episode::create($episode);
        $request->session()->flash('info', 'Der Eintrag wurde gespeichert.');
        return redirect(action('EmployeeController@showEpisodes', $episode['employee_id']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $episode = Episode::findOrFail($id);
        // Get the comments for the select box
        $comments = Comment::all()->lists('comment', 'id')->toArray();
        // Add an empty comment
        $comments[0] = '--';
        // Sort by comment, maintaining the index association
        asort($comments);
        // Get the staffgroups for the select box
        $staffgroups = Staffgroup::all()->sortBy('weight')
            ->lists('staffgroup', 'id')->toArray();
        // Allow from the beginning of database storage
        $start_year = Helper::$firstYear;
        // ... to next year
        $end_year = date('Y') + 1;
        // Turn the start_date field into year and month for the form
        list($episode->year, $episode->month) = explode('-', $episode->start_date);
        return view('episodes.edit', compact(
            'episode', 'comments', 'staffgroups', 'start_year', 'end_year'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $episode = Episode::findOrFail($id);
        // Set the month to the formatted string for database storage.
        $start_date = Helper::validateAndFormatDate($request->get('year'), $request->get('month'));
        // Set the start_date to the database format YYYY-MM.
        $request->merge(['start_date' => $start_date]);
        $episode->update($request->all());
        $request->session()->flash('info', 'Der Eintrag wurde geändert.');
        return redirect(action('EmployeeController@showEpisodes', $episode->employee_id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $episode = Episode::findOrFail($id);
        Episode::destroy($id);
        $request->session()->flash('info', 'Der Eintrag wurde gelöscht.');
        // Remove employee if the last episode has been removed.
        $next_episode = Episode::where('employee_id', $episode->employee_id)->first();
        if (!$next_episode) {
            Employee::where('id', $episode->employee_id)->delete();
            return redirect(action('EmployeeController@index'));
        }
        return redirect(action('EmployeeController@showEpisodes', $episode->employee_id));
    }
}
