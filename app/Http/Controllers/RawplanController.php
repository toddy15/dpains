<?php

namespace App\Http\Controllers;

use App\Models\Rawplan;
use App\Services\Helper;
use App\Services\Planparser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RawplanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Helper $helper): View
    {
        $month_names = [];
        // Allow anon reporting from the beginning of database storage
        $start_year = $helper->firstYear;
        // ... to next year
        $end_year = Carbon::now()->addYear()->yearIso;
        // Determine the current month for anon reporting
        $current_anon_month = Rawplan::where('anon_report', true)->max('month');
        // Format accordingly
        if ($current_anon_month) {
            [$current_anon_year, $current_anon_month] = explode(
                '-',
                (string) $current_anon_month,
            );
        } else {
            $current_anon_month = '00';
            $current_anon_year = '0000';
        }
        $worked_month = $helper->getWorkedMonth();
        if ($worked_month == null) {
            $worked_month = '0000-00';
        }
        // Differentiate between months which are still ongoing ...
        $rawplans_planned = Rawplan::latest('month')
            ->where('month', '>', $worked_month)
            ->get();
        // ... and months which are in the past and won't change.
        // This is just for a nice colouring in the view.
        $rawplans_worked = Rawplan::latest('month')
            ->where('month', '<=', $worked_month)
            ->get();

        // Set up readable month names
        Carbon::setLocale('de');
        for ($m = 1; $m <= 12; $m++) {
            $month_names[$m] = Carbon::createFromDate(2022, $m, 1)
                ->isoFormat('MMMM');
        }

        return view('rawplans.index',
            [
                'month_names' => $month_names,
                'start_year' => $start_year,
                'end_year' => $end_year,
                'current_anon_month' => $current_anon_month,
                'current_anon_year' => $current_anon_year,
                'rawplans_planned' => $rawplans_planned,
                'rawplans_worked' => $rawplans_worked,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Helper $helper): View
    {
        $month_names = [];
        // Allow from the beginning of database storage
        $start_year = $helper->firstYear;
        // ... to next year
        $end_year = Carbon::now()->addYear()->yearIso;
        // Select highest planned month, either in the next year ...
        $year = Carbon::now()->addYear()->yearIso;
        $month = $helper->getPlannedMonth($year);
        while (is_null($month)) {
            // ... or, if not yet planned, in this or previous years.
            $year--;
            $month = $helper->getPlannedMonth($year);
        }
        [$selected_year, $selected_month] = explode('-', $month);

        Carbon::setLocale('de');
        for ($m = 1; $m <= 12; $m++) {
            $month_names[$m] = Carbon::createFromDate(2022, $m, 1)
                ->isoFormat('MMMM');
        }

        return view('rawplans.create',
            [
                'start_year' => $start_year,
                'end_year' => $end_year,
                'month_names' => $month_names,
                'selected_year' => $selected_year,
                'selected_month' => $selected_month,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Helper $helper, Request $request): RedirectResponse
    {
        // Perform validation before actually saving entry.
        $validator = Validator::make($request->all(), [
            'month' => 'required',
            'year' => 'required',
            'people' => 'required',
            'shifts' => 'required',
        ]);
        // Set the month to the formatted string for database storage.
        $month = $helper->validateAndFormatDate(
            (int) $request->input('year'),
            (int) $request->input('month'),
        );
        $planparser = new Planparser(
            $month,
            $request->input('people'),
            $request->input('shifts'),
        );
        // Extend with custom validation rules
        // In the first attempt to validate, check for
        // recognized people and shifts.
        $validator->after(function ($validator) use ($planparser) {
            // Check that the given people match the expected people.
            $error_messages = $planparser->validatePeople();
            foreach ($error_messages as $error_message) {
                $validator->errors()->add('people', $error_message);
            }
            // Check that the given shifts match the expected shifts.
            $error_messages = $planparser->validateShifts();
            foreach ($error_messages as $error_message) {
                $validator->errors()->add('shifts', $error_message);
            }
        });
        // Determine whether there was an error.
        if ($validator->fails()) {
            return to_route('rawplans.create')
                ->withErrors($validator)
                ->withInput();
        }
        // No error so far, so store the shifts.
        $planparser->storeShiftsForPeople();
        // Update the month to the database format YYYY-MM.
        $request->merge(['month' => $month]);
        // Check if there is already an entry in the database,
        // if so, update it.
        $rawplan = Rawplan::where('month', $month)->first();
        if (! $rawplan) {
            // @TODO: Do not inject the anon field
            $request->merge(['anon_report' => false]);
            $rawplan = Rawplan::create($request->all());
        } else {
            $rawplan->update($request->all());
        }
        $rawplan->save();
        $request->session()->flash('info', 'Der Dienstplan wurde gespeichert.');

        return to_route('rawplans.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Helper $helper, Request $request, int $id): RedirectResponse
    {
        $rawplan = Rawplan::findOrFail($id);
        // Only delete the rawplan if it's still in progress
        // and has not been worked through completely.
        if ($rawplan->month <= $helper->getWorkedMonth()) {
            $request
                ->session()
                ->flash('warning', 'Der Dienstplan wurde nicht gelöscht.');

            return redirect(action([\App\Http\Controllers\RawplanController::class, 'index']));
        }
        // Also delete every parsed plan ...
        DB::table('analyzed_months')
            ->where('month', $rawplan->month)
            ->delete();
        $rawplan->delete();
        $request->session()->flash('info', 'Der Dienstplan wurde gelöscht.');

        return to_route('rawplans.index');
    }

    /**
     * Flip the status of inclusion for the month in the anonymous report.
     */
    public function setAnonReportMonth(Helper $helper, Request $request): RedirectResponse
    {
        // Format the month
        $formatted_month = $helper->validateAndFormatDate(
            (int) $request->year,
            (int) $request->month,
        );
        // Update table: Set anon_report to true for all previous months ...
        DB::table('rawplans')
            ->where('month', '<=', $formatted_month)
            ->update(['anon_report' => 1]);
        // ... and to false for all following months
        DB::table('rawplans')
            ->where('month', '>', $formatted_month)
            ->update(['anon_report' => 0]);
        $request
            ->session()
            ->flash(
                'info',
                'Der Status der anonymen Auswertung wurde geändert.',
            );

        return to_route('rawplans.index');
    }
}
