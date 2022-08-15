<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\Helper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PastEmployeeController
{
    public function index(): View
    {
        $employees = Employee::all();
        $current_month = Carbon::now()->isoFormat('YYYY-MM');
        $past_people = Helper::getPastPeople($current_month)->toArray();
        // Construct an array with id, name, and email address
        $past = array_map(function ($employee) use ($employees) {
            // Extract information from employee table
            $data = $employees->where('id', $employee->employee_id)->first();
            // Warn if people *do* have a valid email -- past employees should not.
            $warning = Str::contains($data->email, '@');

            return (object) [
                'id' => $employee->employee_id,
                'name' => $employee->name,
                'email' => $data->email,
                'warning' => $warning,
            ];
        }, $past_people);

        return view('employees.past', ['past' => $past]);
    }
}
