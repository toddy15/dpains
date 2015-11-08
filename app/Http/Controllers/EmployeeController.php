<?php

namespace App\Http\Controllers;

use App\Dpains\Helper;
use App\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;

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
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showEpisodes($id)
    {
        $employee = Employee::findOrFail($id);
        $episodes = $employee->episodes()->orderBy('start_date')->get();
        $latest_name = $employee->name;
        return view('employees.show_episodes', compact('episodes', 'id', 'latest_name'));
    }

    /**
     * Show the employees working in the given month with their
     * calculated night shifts and nef shifts.
     *
     * @param $year
     * @param $month
     * @return mixed
     */
    public function showMonth($year, $month)
    {
        $formatted_month = Helper::validateAndFormatDate($year, $month);
        // Get all episodes valid in this month
        $episodes = Helper::getPeopleForMonth($formatted_month);
        // Get all changes in this month
        $episode_changes = Helper::getChangesForMonth($formatted_month);
        // Set up a readable month name
        $readable_month = Carbon::createFromDate($year, $month)->formatLocalized('%B %Y');
        // Generate the next and previous month urls
        $next_month_url = Helper::getNextMonthUrl('employee/month/', $year, $month);
        $previous_month_url = Helper::getPreviousMonthUrl('employee/month/', $year, $month);
        return view('employees.show_month', compact('episode_changes', 'episodes',
            'readable_month', 'next_month_url', 'previous_month_url'));
    }
}
