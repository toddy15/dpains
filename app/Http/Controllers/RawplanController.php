<?php

namespace App\Http\Controllers;

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
        return view('rawplans.create');
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
            'people' => 'required',
            'shifts' => 'required',
        ]);
        // Check if there is already an entry in the database,
        // if so, update it.
        $month = $request->get('month');
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
