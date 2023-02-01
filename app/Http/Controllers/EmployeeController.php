<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Services\Helper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
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
            $data = $employees->where('id', $employee->employee_id)->firstOrFail();
            $bu = $this->_calculateBUStart();
            $bu_start = $bu[$data->bu_start];
            if (Helper::staffgroupMayReceiveEMail($employee->staffgroup_id)) {
                // Warn if people do *not* have a valid email, although they should.
                $warning = ! Str::contains($data->email, '@');
            } else {
                // Warn if people *do* have a valid email, although they should not.
                $warning = Str::contains($data->email, '@');
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
        $current_ids = array_map(fn ($employee) => $employee->id, $current);
        $future = $employees
            ->filter(fn ($employee) => ! in_array($employee->id, $current_ids))
            ->sortBy('name');
        // Exclude the past employees
        $past_ids = array_map(
            fn ($employee) => $employee->employee_id,
            $past_people,
        );
        $future = $future
            ->filter(fn ($employee) => ! in_array($employee->id, $past_ids))
            ->sortBy('name');

        return view('employees.index', ['current' => $current, 'future' => $future]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $employee = Employee::findOrFail($id);
        $bu = $this->_calculateBUStart();

        return view('employees.edit', ['employee' => $employee, 'bu' => $bu]);
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @throws ValidationException
     */
    public function update(UpdateEmployeeRequest $request, int $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);
        // Generate a new hash with some pseudo random bits.
        // This way, people with "Vertragsende" can no longer
        // access this site.
        $employee->hash = Str::random();
        $employee->update($request->all());
        $request->session()->flash('info', 'Der Mitarbeiter wurde geändert.');

        return to_route('employees.index');
    }

    /**
     * Show the employees working in the given month with their
     * calculated night shifts and nef shifts.
     */
    public function showMonth(int $year, int $month): View
    {
        $formatted_month = Helper::validateAndFormatDate($year, $month);
        // Get all episodes valid in this month
        $episodes = Helper::getPeopleForMonth($formatted_month);
        // Get all changes in this month
        $episode_changes = Helper::getChangesForMonth($formatted_month);
        // Set up a readable month name
        Carbon::setLocale('de');
        $readable_month = Carbon::createFromDate($year, $month, 1)
            ->isoFormat('MMMM YYYY');
        // Generate the next and previous month urls
        $next_month_url = Helper::getNextMonthUrl(
            'employees/month/',
            $year,
            $month,
        );
        $previous_month_url = Helper::getPreviousMonthUrl(
            'employees/month/',
            $year,
            $month,
        );

        return view('employees.show_month',
            [
                'episode_changes' => $episode_changes,
                'episodes' => $episodes,
                'readable_month' => $readable_month,
                'next_month_url' => $next_month_url,
                'previous_month_url' => $previous_month_url,
            ]
        );
    }

    /**
     * Show the employees working in the given year with their
     * VK, grouped by staffgroups.
     */
    public function showVKForYear(string $which_vk, int $year): View
    {
        // Set up result arrays
        $staffgroups = [];
        $vk_per_month = [];
        Helper::sumUpVKForYear($which_vk, $year, $staffgroups, $vk_per_month);
        // Generate the next and previous year urls
        $next_year_url = Helper::getNextYearUrl(
            'employees/vk/'.$which_vk.'/',
            $year,
        );
        $previous_year_url = Helper::getPreviousYearUrl(
            'employees/vk/'.$which_vk.'/',
            $year,
        );

        return view('employees.show_vk_for_year',
            [
                'which_vk' => $which_vk,
                'year' => $year,
                'staffgroups' => $staffgroups,
                'vk_per_month' => $vk_per_month,
                'next_year_url' => $next_year_url,
                'previous_year_url' => $previous_year_url,
            ]
        );
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
            $bu['even'] =
                'Gerades Jahr ('.
                ($current_year - 1).
                ' - '.
                $current_year.
                ')';
            // Otherwise, it's this and next year.
            $bu['odd'] =
                'Ungerades Jahr ('.
                $current_year.
                ' - '.
                ($current_year + 1).
                ')';
        } else {
            // Current year is even.
            // If the BU start is even, it's this and next year.
            $bu['even'] =
                'Gerades Jahr ('.
                $current_year.
                ' - '.
                ($current_year + 1).
                ')';
            // Otherwise, it's last and this year.
            $bu['odd'] =
                'Ungerades Jahr ('.
                ($current_year - 1).
                ' - '.
                $current_year.
                ')';
        }

        return $bu;
    }
}
