<?php

namespace App\Http\Controllers;

use App\Dpains\Helper;
use App\Dpains\Planparser;
use App\Dpains\Reporter;
use App\Rawplan;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RawplanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $worked_month = Helper::getWorkedMonth();
        if ($worked_month == null) {
            $worked_month = '0000-00';
        }
        // Differentiate between months which are still ongoing ...
        $rawplans_planned = Rawplan::orderBy('month', 'desc')
            ->where('month', '>', $worked_month)->get();
        // ... and months which are in the past and won't change.
        // This is just for a nice colouring in the view.
        $rawplans_worked = Rawplan::orderBy('month', 'desc')
            ->where('month', '<=', $worked_month)->get();
        return view('rawplans.index', compact('rawplans_planned', 'rawplans_worked'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        // Allow from the beginning of database storage
        $start_year = Helper::$firstYear;
        // ... to next year
        $end_year = date('Y') + 1;
        return view('rawplans.create', compact('start_year', 'end_year'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Perform validation before actually saving entry.
        $validator = Validator::make($request->all(), [
            'month' => 'required',
            'year' => 'required',
            'people' => 'required',
            'shifts' => 'required',
        ]);
        // Set the month to the formatted string for database storage.
        $month = Helper::validateAndFormatDate($request->get('year'), $request->get('month'));
        $planparser = new Planparser($month, $request->all());
        // Extend with custom validation rules
        // In the first attempt to validate, check for
        // recognized people and shifts.
        $validator->after(function($validator) use ($planparser) {
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
            return redirect(action('RawplanController@create'))
                ->withErrors($validator)
                ->withInput();
        }
        // No error so far, so try to store the shifts.
        $validator->after(function($validator) use ($planparser) {
            // Parse the plan and save it.
            $error_message = $planparser->storeShiftsForPeople();
            if (!empty($error_message)) {
                $validator->errors()->add('shifts', $error_message);
            }
        });
        // Determine whether there was an error.
        if ($validator->fails()) {
            return redirect(action('RawplanController@create'))
                ->withErrors($validator)
                ->withInput();
        }
        // Update the month to the database format YYYY-MM.
        $request->merge(['month' => $month]);
        // Check if there is already an entry in the database,
        // if so, update it.
        $rawplan = Rawplan::where('month', $month)->first();
        if (!$rawplan) {
            $rawplan = Rawplan::create($request->all());
        } else {
            $rawplan->update($request->all());
        }
        $rawplan->save();
        $request->session()->flash('info', 'Der Dienstplan wurde gespeichert.');
        return redirect(action('RawplanController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $rawplan = Rawplan::findOrFail($id);
        // Also delete every parsed plan ...
        DB::table('analyzed_months')->where('month', $rawplan->month)->delete();
        $rawplan->delete();
        $request->session()->flash('info', 'Der Dienstplan wurde gelöscht.');
        return redirect(action('RawplanController@index'));
    }

    /**
     * Flip the status of inclusion for the month in the anonymous report.
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function flipAnonReport(Request $request, $id)
    {
        $rawplan = Rawplan::findOrFail($id);
        $rawplan->anon_report = $rawplan->anon_report ? false : true;
        // Do not update timestamps for this change.
        $rawplan->timestamps = false;
        $rawplan->save();
        $request->session()->flash('info', 'Der Status der anonymen Auswertung wurde geändert.');
        return redirect(action('RawplanController@index'));
    }
}
