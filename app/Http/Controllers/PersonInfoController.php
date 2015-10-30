<?php

namespace App\Http\Controllers;

use App\Episode;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PersonInfoController extends Controller
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
        // Get the name of the latest episode.
        $latest_name = $episodes->last()->name;
        return view('people.show', compact('episodes', 'number', 'latest_name'));
    }

    /**
     * Show all episodes for a person, using anonymous access.
     *
     * The hash is mapped to the person's number.
     *
     * @param string $hash
     * @return \Illuminate\View\View
     */
    public function anonEpisodes($hash)
    {
        $number = 1;
        return $this->show($number);
    }

    /**
     * Request a new hash via mail for accessing the stats.
     */
    public function requestNewHashPerMail(Request $request) {
        return redirect(url('/'));
    }
}
