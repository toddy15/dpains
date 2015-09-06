<?php

namespace App\Http\Controllers;

use App\Episode;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PersonController extends Controller
{
    public function index($number)
    {
        $episodes = Episode::where('number', '=', $number)->get();
        if (!count($episodes)) {
            abort(404);
        }
        return view('people.index', compact('episodes', 'number'));
    }
}
