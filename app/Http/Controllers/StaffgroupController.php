<?php

namespace App\Http\Controllers;

use App\Models\Staffgroup;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StaffgroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $staffgroups = Staffgroup::all()->sortBy('weight');

        return view('staffgroups.index', compact('staffgroups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('staffgroups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'staffgroup' => 'required',
            'weight' => 'required|numeric',
        ]);
        Staffgroup::create($request->all());
        $request->session()->flash('info', 'Die Mitarbeitergruppe wurde gespeichert.');

        return redirect(action([StaffgroupController::class, 'index']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $staffgroup = Staffgroup::findOrFail($id);

        return view('staffgroups.edit', compact('staffgroup'));
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
            'staffgroup' => 'required',
            'weight' => 'required|numeric',
        ]);
        $staffgroup = Staffgroup::findOrFail($id);
        $staffgroup->update($request->all());
        $request->session()->flash('info', 'Die Mitarbeitergruppe wurde geändert.');

        return redirect(action([StaffgroupController::class, 'index']));
    }
}
