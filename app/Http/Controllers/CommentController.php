<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $comments = Comment::all()->sortBy('comment');

        return view('comments.index', ['comments' => $comments]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('comments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'comment' => 'required',
        ]);
        Comment::create($request->all());
        $request->session()->flash('info', 'Die Bemerkung wurde gespeichert.');

        return to_route('comments.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $comment = Comment::findOrFail($id);

        return view('comments.edit', ['comment' => $comment]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws ValidationException
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->validate($request, [
            'comment' => 'required',
        ]);

        $comment = Comment::findOrFail($id);
        $comment->update($request->all());
        $request->session()->flash('info', 'Die Bemerkung wurde geändert.');

        return to_route('comments.index');
    }
}
