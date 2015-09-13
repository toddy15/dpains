<?php

namespace App\Http\Controllers;

use App\Dpains\Planparser;
use App\Dpains\Reporter;
use App\Rawplan;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        $planparser = new Planparser('2015-01');
        $rawplan = Rawplan::where('month', '2015-01')->first();
        $planparser->parseNames($rawplan->people);
        $planparser->parseShifts($rawplan->shifts);
        $planparser->storeShiftsForPeople();
    }

    public function show($year, $month)
    {
        $reporter = new Reporter();
        $formatted_month = $reporter->validateAndFormatDate($year, $month);
        return $reporter->getNamesForMonth($formatted_month);
    }
}
