<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffgroupRequest;
use App\Models\Staffgroup;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StaffgroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $staffgroups = Staffgroup::all()->sortBy('weight');

        return view('staffgroups.index', ['staffgroups' => $staffgroups]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('staffgroups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @throws ValidationException
     */
    public function store(StaffgroupRequest $request): RedirectResponse
    {
        Staffgroup::create($request->all());
        $request
            ->session()
            ->flash('info', 'Die Mitarbeitergruppe wurde gespeichert.');

        return to_route('staffgroups.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $staffgroup = Staffgroup::findOrFail($id);

        return view('staffgroups.edit', ['staffgroup' => $staffgroup]);
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @throws ValidationException
     */
    public function update(StaffgroupRequest $request, int $id): RedirectResponse
    {
        $staffgroup = Staffgroup::findOrFail($id);
        $staffgroup->update($request->all());
        $request
            ->session()
            ->flash('info', 'Die Mitarbeitergruppe wurde geändert.');

        return to_route('staffgroups.index');
    }
}
