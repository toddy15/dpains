<?php

namespace App\Http\Controllers;

use App\Dpains\Helper;
use App\DueShift;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DueShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Sort with a callback to respect both year and staffgroup
        $due_shifts = DueShift::all()->sort(function ($a, $b) {
            // First sort by year
            if ($a->year == $b->year) {
                // Same year, sort by staffgroup weight
                if ($a->staffgroup['weight'] == $b->staffgroup['weight']) {
                    // Normally, this should not happen ...
                    return 0;
                }
                return ($a->staffgroup['weight'] < $b->staffgroup['weight']) ? -1 : 1;
            }
            // Sort the year descending
            return ($a->year < $b->year) ? 1 : -1;
        });
        return view('due_shifts.index', compact('due_shifts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('due_shifts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'year' => 'required|numeric|min:' . Helper::$firstYear,
            'nights' => 'required|numeric',
            'nefs' => 'required|numeric',
        ]);
        DueShift::create($request->all());
        $request->session()->flash('info', 'Die Sollzahlen wurden gespeichert.');
        return redirect(action('DueShiftController@index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $due_shift= DueShift::findOrFail($id);
        return view('due_shifts.edit', compact('due_shift'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $due_shift = DueShift::findOrFail($id);
        $this->validate($request, [
            'year' => 'required|numeric|min:' . Helper::$firstYear,
            'nights' => 'required|numeric',
            'nefs' => 'required|numeric',
        ]);
        $due_shift->update($request->all());
        $request->session()->flash('info', 'Die Sollzahlen wurden geändert.');
        return redirect(action('DueShiftController@index'));
    }
}
