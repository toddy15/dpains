<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Employee;
use App\Models\Episode;
use App\Models\Staffgroup;
use App\Services\Helper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EpisodeController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  Request  $request
     * @return View
     */
    public function create(Request $request): View
    {
        // See if there is a valid employee
        $employee_id = (int) $request->get('employee_id');
        // Is there already an episode for this employee?
        // If yes, retrieve the latest episode for the default values.
        $episode = Episode::where('employee_id', $employee_id)
            ->latest('start_date')
            ->first();
        if (! $episode) {
            // There are no episodes, so create a new employee
            // using sane default values.
            $wb_id = Staffgroup::where('staffgroup', 'WB')->first()->id;
            $episode = new Episode();
            $episode->start_date = Carbon::now()->isoFormat('YYYY-MM');
            $episode->staffgroup_id = $wb_id;
            $episode->vk = 1.0;
            $episode->factor_night = 0.0;
            $episode->factor_nef = 0.0;
        }
        // Get values for the select boxes
        $comments = Comment::all()->sortBy('comment');
        $staffgroups = Staffgroup::all()->sortBy('weight');
        // Allow from the beginning of database storage or some years back
        $start_year = max(
            Helper::$firstYear,
            Carbon::now()->subYears(3)->yearIso,
        );
        // ... to some years ahead
        $end_year = Carbon::now()->addYears(3)->yearIso;

        for ($m = 1; $m <= 12; $m++) {
            $month_names[$m] = Carbon::create(2022, $m)
                ->locale('de')
                ->isoFormat('MMMM');
        }

        return view(
            'episodes.create',
            compact(
                'episode',
                'comments',
                'staffgroups',
                'start_year',
                'end_year',
                'month_names',
            ),
        );
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
            'name' => 'required',
            'month' => 'required',
            'year' => 'required',
            'vk' => 'required|numeric|between:0,1',
            'factor_night' => 'required|numeric|between:0,2',
            'factor_nef' => 'required|numeric|between:0,2',
        ]);
        // Set the month to the formatted string for database storage.
        $start_date = Helper::validateAndFormatDate(
            $request->get('year'),
            $request->get('month'),
        );
        // Set the start_date to the database format YYYY-MM.
        $request->merge(['start_date' => $start_date]);
        // Check if the episode is for a new employee
        $episode = $request->all();
        if ($episode['employee_id'] == 0) {
            // This is a new employee, so create a new entry.
            // The BU cycle always starts in the next year.
            if ((int) $request->get('year') % 2 === 0) {
                // Currently an even year, so start cycle next year (odd)
                $bu_start = 'odd';
            } else {
                // Currently an odd year, so start cycle next year (even)
                $bu_start = 'even';
            }
            $employee = Employee::create([
                'email' => $episode['name'],
                'hash' => Str::random(),
                'bu_start' => $bu_start,
            ]);
            $episode['employee_id'] = $employee->id;
        }
        Episode::create($episode);
        $request->session()->flash('info', 'Der Eintrag wurde gespeichert.');

        return to_route('employees.episodes.index', [
            'employee' => $episode['employee_id'],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function edit(int $id): View
    {
        $episode = Episode::findOrFail($id);
        // Get values for the select boxes
        $comments = Comment::all()->sortBy('comment');
        $staffgroups = Staffgroup::all()->sortBy('weight');
        // Allow from the beginning of database storage
        $start_year = Helper::$firstYear;
        // ... to some years ahead
        $end_year = Carbon::now()->addYears(3)->yearIso;

        for ($m = 1; $m <= 12; $m++) {
            $month_names[$m] = Carbon::create(2022, $m)
                ->locale('de')
                ->isoFormat('MMMM');
        }

        return view(
            'episodes.edit',
            compact(
                'episode',
                'comments',
                'staffgroups',
                'start_year',
                'end_year',
                'month_names',
            ),
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $episode = Episode::findOrFail($id);
        // Set the month to the formatted string for database storage.
        $start_date = Helper::validateAndFormatDate(
            $request->get('year'),
            $request->get('month'),
        );
        // Set the start_date to the database format YYYY-MM.
        $request->merge(['start_date' => $start_date]);
        $episode->update($request->all());
        $request->session()->flash('info', 'Der Eintrag wurde geändert.');

        return to_route('employees.episodes.index', [
            'employee' => $episode->employee_id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $episode = Episode::findOrFail($id);
        Episode::destroy($id);
        $request->session()->flash('info', 'Der Eintrag wurde gelöscht.');
        // Remove employee if the last episode has been removed.
        $next_episode = Episode::where(
            'employee_id',
            $episode->employee_id,
        )->first();
        if (! $next_episode) {
            Employee::where('id', $episode->employee_id)->delete();

            return redirect(action([EmployeeController::class, 'index']));
        }

        return to_route('employees.episodes.index', [
            'employee' => $episode->employee_id,
        ]);
    }
}
