<?php

namespace App\Http\Controllers;

use App\Dpains\Planparser;
use App\Dpains\Reporter;
use App\Rawplan;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
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
        $rawplans = Rawplan::orderBy('month', 'desc')->get();
        return view('rawplans.index', compact('rawplans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        // Allow from the beginning of database storage
        $start_year = Reporter::$firstYear;
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
        $planparser = new Planparser($request->all());
        // Extend with custom validation rules
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
        // Set the month to the formatted string for database storage.
        $month = Reporter::validateAndFormatDate($request->get('year'), $request->get('month'));
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
        // Parse the plan and save it.
        $planparser->storeShiftsForPeople();
        return redirect(action('RawplanController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Rawplan::destroy($id);
        return redirect(action('RawplanController@index'));
    }
}
