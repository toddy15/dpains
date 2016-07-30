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
        // Display current employees first, already sorted by staffgroup and name
        $current_month = date("Y-m");
        $current = Helper::getPeopleForMonth($current_month);
        // Add the email address
        foreach ($current as $index => $employee) {
            $employee_with_email = $employee;
            $employee_with_email->email = $employees->where('id', $employee->employee_id)->pluck('email')[0];
            $current[$index] = $employee_with_email;
        }
        return view('employees.index', compact('current'));
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
            'email' => 'required'
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

    /**
     * Show the employees working in the given year with their
     * VK, grouped by staffgroups.
     *
     * @param $which_vk
     * @param $year
     * @return mixed
     */
    public function showVKForYear($which_vk, $year)
    {
        // Set up result arrays
        $staffgroups = [];
        $vk_per_month = [];
        Helper::sumUpVKForYear($which_vk, $year, $staffgroups, $vk_per_month);
        // Generate the next and previous year urls
        $next_year_url = Helper::getNextYearUrl('employee/vk/'. $which_vk . '/', $year);
        $previous_year_url = Helper::getPreviousYearUrl('employee/vk/'. $which_vk . '/', $year);
        return view('employees.show_vk_for_year', compact('which_vk', 'year', 'staffgroups',
            'vk_per_month', 'next_year_url', 'previous_year_url'));
    }
}
