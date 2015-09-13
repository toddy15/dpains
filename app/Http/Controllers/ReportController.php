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

class ReportController extends Controller
{
    public function index()
    {
        $rawplan = Rawplan::where('month', '2015-09')->first();

        dd($rawplan);
    }

    public function show($year, $month)
    {
        $planparser = new Planparser();

        $reporter = new Reporter();
        $formatted_month = Helper::validateAndFormatDate($year, $month);
        $data = DB::table('analyzed_months')
            ->where('month', $formatted_month)->get();
        dd($data);
        return $reporter->getNamesForMonth($formatted_month);
    }
}
