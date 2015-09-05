<?php

namespace App\Http\Controllers;

use App\Episode;
use App\Comment;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class EpisodeController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $comments = Comment::all()->sortBy('comment')
            ->lists('comment', 'id')->toArray();
        return view('episodes.create', compact('comments'));
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
        Episode::create($request->all());
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
        $comments = Comment::all()->sortBy('comment')
            ->lists('comment', 'id')->toArray();
        return view('episodes.edit', compact('episode', 'comments'));
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        Episode::destroy($id);
    }
}
