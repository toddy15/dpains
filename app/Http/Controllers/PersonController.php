<?php

namespace App\Http\Controllers;

use App\Episode;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PersonController extends Controller
{
    public function show($number)
    {
        $episodes = Episode::where('number', '=', $number)->get();
        if (!count($episodes)) {
            abort(404);
        }
        return view('people.show', compact('episodes', 'number'));
    }
}
