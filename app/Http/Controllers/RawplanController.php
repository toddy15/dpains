<?php

namespace App\Http\Controllers;

use App\Dpains\Reporter;
use App\Rawplan;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class RawplanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $rawplans = Rawplan::orderBy('month', 'desc')->get();
        return view('rawplans.index', compact('rawplans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        // Allow from the beginning of database storage
        $start_year = Reporter::$firstYear;
        // ... to next year
        $end_year = date('Y') + 1;
        return view('rawplans.create', compact('start_year', 'end_year'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'month' => 'required',
            'year' => 'required',
            'people' => 'required',
            'shifts' => 'required',
        ]);
        // Check if there is already an entry in the database,
        // if so, update it.
        $month = Reporter::validateAndFormatDate($request->get('year'), $request->get('month'));
        // Set the month to the formatted string for database storage.
        $request->merge(['month' => $month]);
        $rawplan = Rawplan::where('month', $month)->first();
        if (!$rawplan) {
            $rawplan = Rawplan::create($request->all());
        } else {
            $rawplan->update($request->all());
        }
        $rawplan->save();
        return redirect(action('RawplanController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Rawplan::destroy($id);
        return redirect(action('RawplanController@index'));
    }
}
