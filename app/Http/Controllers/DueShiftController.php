<?php

namespace App\Http\Controllers;

use App\Http\Requests\DueShiftRequest;
use App\Models\DueShift;
use App\Models\Staffgroup;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DueShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Sort with a callback to respect both year and staffgroup
        $due_shifts = DueShift::all()->sort(function ($a, $b): int {
            // First sort by year
            if ($a->year === $b->year) {
                // Same year, sort by staffgroup weight ascending
                if ($a->staffgroup != null and $b->staffgroup != null) {
                    return $a->staffgroup->weight <=> $b->staffgroup->weight;
                } else {
                    return 0;
                }
            }

            // Sort the year descending
            return $b->year <=> $a->year;
        });

        return view('due_shifts.index', ['due_shifts' => $due_shifts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $staffgroups = Staffgroup::all()->sortBy('weight');

        // Special case: Merge "FA" and "WB mit Nachtdiensten"
        foreach ($staffgroups as $key => $staffgroup) {
            if ($staffgroup->staffgroup == 'FA') {
                $staffgroup->staffgroup = 'FA und WB mit Nachtdienst';
            }

            if ($staffgroup->staffgroup == 'WB mit Nachtdienst') {
                $staffgroups->forget($key);
            }
        }

        return view('due_shifts.create', ['staffgroups' => $staffgroups]);
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @throws ValidationException
     */
    public function store(DueShiftRequest $request): RedirectResponse
    {
        // If the combination year and staffgroup is not unique,
        // this will throw a QueryException.
        try {
            DueShift::create($request->all());
            $request
                ->session()
                ->flash('info', 'Die Sollzahlen wurden gespeichert.');
        } catch (QueryException) {
            $request
                ->session()
                ->flash(
                    'danger',
                    'Die Sollzahlen für das Jahr und die Gruppe existieren bereits, es wurde nichts geändert.',
                );
        }

        return to_route('due_shifts.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $due_shift = DueShift::findOrFail($id);
        $staffgroups = Staffgroup::all()->sortBy('weight');

        // Special case: Merge "FA" and "WB mit Nachtdiensten"
        foreach ($staffgroups as $key => $staffgroup) {
            if ($staffgroup->staffgroup == 'FA') {
                $staffgroup->staffgroup = 'FA und WB mit Nachtdienst';
            }

            if ($staffgroup->staffgroup == 'WB mit Nachtdienst') {
                $staffgroups->forget($key);
            }
        }

        return view('due_shifts.edit', ['due_shift' => $due_shift, 'staffgroups' => $staffgroups]);
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @throws ValidationException
     */
    public function update(DueShiftRequest $request, int $id): RedirectResponse
    {
        $due_shift = DueShift::findOrFail($id);
        // If the combination year and staffgroup is not unique,
        // this will throw a QueryException.
        try {
            $due_shift->update($request->all());
            $request
                ->session()
                ->flash('info', 'Die Sollzahlen wurden geändert.');
        } catch (QueryException) {
            $request
                ->session()
                ->flash(
                    'danger',
                    'Die Sollzahlen für das Jahr und die Gruppe existieren bereits, es wurde nichts geändert.',
                );
        }

        return to_route('due_shifts.index');
    }
}
