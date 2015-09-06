<?php

namespace App\Http\Controllers;

use App\Episode;
use App\Comment;
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
        if ($number) {
            // Are there already episodes for this person's number?
            $episodes = Episode::where('number', '=', $number)->get();
            if (!count($episodes)) {
                // There are not episodes, so create a new person by
                // setting the number to 0.
                $number = 0;
            }
        }
        $comments = Comment::all()->sortBy('comment')
            ->lists('comment', 'id')->toArray();
        return view('episodes.create', compact('comments', 'number'));
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
        $comments = Comment::all()->sortBy('comment')
            ->lists('comment', 'id')->toArray();
        return view('episodes.edit', compact('episode', 'comments', 'number'));
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
