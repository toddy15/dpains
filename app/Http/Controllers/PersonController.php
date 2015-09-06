<?php

namespace App\Http\Controllers;

use App\Episode;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PersonController extends Controller
{
    /**
     * Show all episodes for the given person number.
     *
     * @param int $number
     * @return \Illuminate\View\View
     */
    public function show($number)
    {
        $episodes = Episode::where('number', '=', $number)
            ->orderBy('start_date')->get();
        if (!count($episodes)) {
            abort(404);
        }
        return view('people.show', compact('episodes', 'number'));
    }
}
