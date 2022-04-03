<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $comments = Comment::all()->sortBy('comment');

        return view('comments.index', compact('comments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('comments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'comment' => 'required',
        ]);
        Comment::create($request->all());
        $request->session()->flash('info', 'Die Bemerkung wurde gespeichert.');

        return redirect(action([CommentController::class, 'index']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $comment = Comment::findOrFail($id);

        return view('comments.edit', compact('comment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $comment = Comment::findOrFail($id);
        $comment->update($request->all());
        $request->session()->flash('info', 'Die Bemerkung wurde geändert.');

        return redirect(action([CommentController::class, 'index']));
    }
}
