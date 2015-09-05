<?php

namespace App\Http\Controllers;

use App\Staffgroup;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class StaffgroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $staffgroups = Staffgroup::all()->sortBy('weight');
        return view('staffgroups.index', compact('staffgroups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('staffgroups.create');
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
            'staffgroup' => 'required',
            'weight' => 'required|numeric',
        ]);
        Staffgroup::create($request->all());
        return redirect(action('StaffgroupController@index'));
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
        $staffgroup = Staffgroup::findOrFail($id);
        return view('staffgroups.edit', compact('staffgroup'));
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
        $this->validate($request, [
            'staffgroup' => 'required',
            'weight' => 'required|numeric',
        ]);
        $staffgroup = Staffgroup::findOrFail($id);
        $staffgroup->update($request->all());
        return redirect(action('StaffgroupController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Staffgroup::destroy($id);
        return redirect(action('StaffgroupController@index'));
    }
}
