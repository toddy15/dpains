<?php

namespace App\Http\Controllers;

use App\Episode;
use App\Comment;
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
        // See if there is a valid person number
        $number = (int)$request->get('number');
        // Is there already an episode for this person's number?
        // If yes, retrieve the latest episode for the default values.
        $episode = Episode::where('number', '=', $number)
            ->orderBy('start_date', 'desc')->first();
        if (!$episode) {
            // There are no episodes, so create a new person by
            // setting the number to 0 and using sane default values.
            $number = 0;
            $episode = new Episode();
            $episode->number = 0;
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
        return view('episodes.create', compact('episode', 'comments', 'staffgroups', 'number'));
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
            'start_date' => 'required|regex:/^2[0-9]{3}-[01][0-9]$/',
            'vk' => 'required|numeric|between:0,1',
            'factor_night' => 'required|numeric|between:0,2',
            'factor_nef' => 'required|numeric|between:0,2',
        ]);
        // Check if the episode is for a new person number
        $episode = $request->all();
        if ($episode['number'] == 0) {
            // Find out the highest number currently in use
            // and create a new person by adding 1.
            $highest_person_number = DB::table('episodes')->max('number');
            $episode['number'] = $highest_person_number + 1;
        }
        Episode::create($episode);
        return redirect(action('PersonController@show', $episode['number']));
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
        $number = $episode->number;
        // Get the comments for the select box
        $comments = Comment::all()->lists('comment', 'id')->toArray();
        // Add an empty comment
        $comments[0] = '--';
        // Sort by comment, maintaining the index association
        asort($comments);
        // Get the staffgroups for the select box
        $staffgroups = Staffgroup::all()->sortBy('weight')
            ->lists('staffgroup', 'id')->toArray();
        return view('episodes.edit', compact('episode', 'comments', 'staffgroups', 'number'));
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
        $episode->update($request->all());
        return redirect(action('PersonController@show', $episode->number));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $episode = Episode::findOrFail($id);
        Episode::destroy($id);
        return redirect(action('PersonController@show', $episode->number));
    }
}
