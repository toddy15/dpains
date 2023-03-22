<?php

namespace App\Http\Controllers;

use App\Staffgroup;
use Illuminate\Http\RedirectResponse;
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
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'staffgroup' => 'required',
            'weight' => 'required|numeric',
        ]);
        Staffgroup::create($request->all());
        $request->session()->flash('info', 'Die Mitarbeitergruppe wurde gespeichert.');
        return redirect(action([StaffgroupController::class, 'index']));
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
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->validate($request, [
            'staffgroup' => 'required',
            'weight' => 'required|numeric',
        ]);
        $staffgroup = Staffgroup::findOrFail($id);
        $staffgroup->update($request->all());
        $request->session()->flash('info', 'Die Mitarbeitergruppe wurde geändert.');
        return redirect(action([StaffgroupController::class, 'index']));
    }
}
