<?php

namespace App\Http\Controllers;

use App\Models\DueShift;
use App\Models\Staffgroup;
use App\Services\Helper;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DueShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        // Sort with a callback to respect both year and staffgroup
        $due_shifts = DueShift::all()->sort(function ($a, $b) {
            // First sort by year
            if ($a->year === $b->year) {
                // Same year, sort by staffgroup weight ascending
                return $a->staffgroup['weight'] <=> $b->staffgroup['weight'];
            }
            // Sort the year descending
            return $b->year <=> $a->year;
        });

        return view('due_shifts.index', compact('due_shifts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $staffgroups = Staffgroup::all()->sortBy('weight');

        // Special case: Merge "FA" and "WB mit Nachtdiensten"
        foreach ($staffgroups as $key => $staffgroup) {
            if ($staffgroup == 'FA') {
                $staffgroups[$key] = 'FA und WB mit Nachtdienst';
            }
            if ($staffgroup == 'WB mit Nachtdienst') {
                unset($staffgroups[$key]);
            }
        }

        return view('due_shifts.create', compact('staffgroups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'year' => 'required|numeric|min:'.Helper::$firstYear,
            'nights' => 'required|numeric',
            'nefs' => 'required|numeric',
        ]);
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
                    'Die Sollzahlen für das Jahr und die Mitarbeitergruppe existieren bereits, es wurde nichts geändert.',
                );
        }

        return to_route('due_shifts.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function edit(int $id): View
    {
        $due_shift = DueShift::findOrFail($id);
        $staffgroups = Staffgroup::all()->sortBy('weight');

        // Special case: Merge "FA" and "WB mit Nachtdiensten"
        foreach ($staffgroups as $key => $staffgroup) {
            if ($staffgroup == 'FA') {
                $staffgroups[$key] = 'FA und WB mit Nachtdienst';
            }
            if ($staffgroup == 'WB mit Nachtdienst') {
                unset($staffgroups[$key]);
            }
        }

        return view('due_shifts.edit', compact('due_shift', 'staffgroups'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $due_shift = DueShift::findOrFail($id);
        $this->validate($request, [
            'year' => 'required|numeric|min:'.Helper::$firstYear,
            'nights' => 'required|numeric',
            'nefs' => 'required|numeric',
        ]);
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
                    'Die Sollzahlen für das Jahr und die Mitarbeitergruppe existieren bereits, es wurde nichts geändert.',
                );
        }

        return to_route('due_shifts.index');
    }
}
