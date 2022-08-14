<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Contracts\View\View;

class EmployeeEpisodeController extends Controller
{
    public function index(Employee $employee): View
    {
        $episodes = $employee
            ->episodes()
            ->oldest('start_date')
            ->get();
        $latest_name = $employee->name;

        return view(
            'employees.show_episodes',
            compact('employee', 'episodes', 'latest_name'),
        );
    }
}
