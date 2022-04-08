<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dpains\Helper;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $employees = Employee::all();
        // Display current employees first, already sorted by staffgroup and name
        $current_month = Carbon::now()->isoFormat('YYYY-MM');
        $people = Helper::getPeopleForMonth($current_month)->toArray();
        // Exclude the past employees.
        $past_people = Helper::getPastPeople($current_month)->toArray();
        // Construct an array with id, name, and email address
        $current = array_map(function ($employee) use ($employees) {
            // Extract information from employee table
            $data = $employees->where('id', $employee->employee_id)->first();
            $bu = $this->_calculateBUStart();
            $bu_start = $bu[$data->bu_start];
            if (Helper::staffgroupMayReceiveEMail($employee->staffgroup_id)) {
                // Warn if people do *not* have a valid email, although they should.
                $warning = ! Str::contains($data->email, "@");
            } else {
                // Warn if people *do* have a valid email, although they should not.
                $warning = Str::contains($data->email, "@");
            }

            return (object) [
                'id' => $employee->employee_id,
                'name' => $employee->name,
                'email' => $data->email,
                'bu_start' => $bu_start,
                'warning' => $warning,
            ];
        }, $people);
        // Now collect all remaining employees
        $current_ids = array_map(fn($employee) => $employee->id, $current);
        $future = $employees->filter(fn($employee) => ! in_array($employee->id, $current_ids))->sortBy('name');
        // Exclude the past employees
        $past_ids = array_map(fn($employee) => $employee->employee_id, $past_people);
        $future = $future->filter(fn($employee) => ! in_array($employee->id, $past_ids))->sortBy('name');

        return view('employees.index', compact('current', 'future'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $employee = Employee::findOrFail($id);
        $bu = $this->_calculateBUStart();

        return view('employees.edit', compact('employee', 'bu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->validate($request, [
            'email' => 'required',
        ]);
        $employee = Employee::findOrFail($id);
        $employee->update($request->all());
        $request->session()->flash('info', 'Der Mitarbeiter wurde geändert.');

        return redirect(action([EmployeeController::class, 'index']));
    }

    /**
     * Show past employees.
     *
     * @param Request $request
     * @return View
     */
    public function showPastEmployees(Request $request): View
    {
        $employees = Employee::all();
        $current_month = Carbon::now()->isoFormat('YYYY-MM');
        $past_people = Helper::getPastPeople($current_month)->toArray();
        // Construct an array with id, name, and email address
        $past = array_map(function ($employee) use ($employees) {
            // Extract information from employee table
            $data = $employees->where('id', $employee->employee_id)->first();
            // Warn if people *do* have a valid email -- past employees should not.
            $warning = Str::contains($data->email, "@");

            return (object) [
                'id' => $employee->employee_id,
                'name' => $employee->name,
                'email' => $data->email,
                'warning' => $warning,
            ];
        }, $past_people);

        return view('employees.past', compact('past'));
    }

    /**
     * Show all episodes for the given employee id.
     *
     * @param int $id
     * @return View
     */
    public function showEpisodes(int $id): View
    {
        $employee = Employee::findOrFail($id);
        $episodes = $employee->episodes()->oldest('start_date')->get();
        $latest_name = $employee->name;

        return view('employees.show_episodes', compact('episodes', 'id', 'latest_name'));
    }

    /**
     * Show the employees working in the given month with their
     * calculated night shifts and nef shifts.
     *
     * @param $year
     * @param $month
     * @return View
     */
    public function showMonth($year, $month): View
    {
        $formatted_month = Helper::validateAndFormatDate($year, $month);
        // Get all episodes valid in this month
        $episodes = Helper::getPeopleForMonth($formatted_month);
        // Get all changes in this month
        $episode_changes = Helper::getChangesForMonth($formatted_month);
        // Set up a readable month name
        $readable_month = Carbon::createFromDate($year, $month)->locale('de')->isoFormat('MMMM YYYY');
        // Generate the next and previous month urls
        $next_month_url = Helper::getNextMonthUrl('employee/month/', $year, $month);
        $previous_month_url = Helper::getPreviousMonthUrl('employee/month/', $year, $month);

        return view('employees.show_month', compact(
            'episode_changes',
            'episodes',
            'readable_month',
            'next_month_url',
            'previous_month_url'
        ));
    }

    /**
     * Show the employees working in the given year with their
     * VK, grouped by staffgroups.
     *
     * @param $which_vk
     * @param $year
     * @return View
     */
    public function showVKForYear($which_vk, $year): View
    {
        // Set up result arrays
        $staffgroups = [];
        $vk_per_month = [];
        Helper::sumUpVKForYear($which_vk, $year, $staffgroups, $vk_per_month);
        // Generate the next and previous year urls
        $next_year_url = Helper::getNextYearUrl('employee/vk/'. $which_vk . '/', $year);
        $previous_year_url = Helper::getPreviousYearUrl('employee/vk/'. $which_vk . '/', $year);

        return view('employees.show_vk_for_year', compact(
            'which_vk',
            'year',
            'staffgroups',
            'vk_per_month',
            'next_year_url',
            'previous_year_url'
        ));
    }

    /**
     * Calculate the string for BU starts
     */
    private function _calculateBUStart(): array
    {
        $current_year = Carbon::now()->yearIso;
        $bu = [];
        $bu[''] = 'Nicht hinterlegt';
        if ($current_year % 2) {
            // Current year is odd
            // If the BU start is even, it's last and this year.
            $bu['even'] = 'Gerades Jahr (' . ($current_year - 1) . ' - ' . $current_year . ')';
            // Otherwise, it's this and next year.
            $bu['odd'] = 'Ungerades Jahr (' . $current_year . ' - ' . ($current_year + 1) . ')';
        } else {
            // Current year is even.
            // If the BU start is even, it's this and next year.
            $bu['even'] = 'Gerades Jahr (' . $current_year . ' - ' . ($current_year + 1) . ')';
            // Otherwise, it's last and this year.
            $bu['odd'] = 'Ungerades Jahr (' . ($current_year - 1) . ' - ' . $current_year . ')';
        }

        return $bu;
    }
}
