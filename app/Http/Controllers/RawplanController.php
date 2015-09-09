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
        Rawplan::create($request->all());
        return redirect(action('RawplanController@index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
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
