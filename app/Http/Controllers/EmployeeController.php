<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Episode;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
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
        $this->validate($request, [
            'email' => 'required|email'
        ]);
        $employee = Employee::findOrFail($id);
        $employee->update($request->all());
        $request->session()->flash('info', 'Der Mitarbeiter wurde geändert.');
        return redirect(action('EmployeeController@index'));
    }

    /**
     * Show all episodes for the given employee id.
     *
     * @param int $employee_id
     * @return \Illuminate\View\View
     */
    public function showEpisodes($employee_id)
    {
        $episodes = Episode::where('employee_id', $employee_id)
            ->orderBy('start_date')->get();
        if (!count($episodes)) {
            abort(404);
        }
        // Get the name of the latest episode.
        $latest_name = $episodes->last()->name;
        return view('employees.show_episodes', compact('episodes', 'employee_id', 'latest_name'));
    }

}
