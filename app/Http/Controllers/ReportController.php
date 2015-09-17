<?php

namespace App\Http\Controllers;

use App\Dpains\Helper;
use App\Dpains\Planparser;
use App\Rawplan;
use Carbon\Carbon;
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
        $formatted_month = Helper::validateAndFormatDate($year, $month);
        // Get information for all people in this month
        $reports = DB::table('analyzed_months')
            ->where('month', $formatted_month)->get();
        // Set up a readable month name
        $readable_month = Carbon::createFromDate($year, $month)->formatLocalized('%B %Y');
        // Generate the next and previous month urls
        $next_month_url = Helper::getNextMonthUrl('report/', $year, $month);
        $previous_month_url = Helper::getPreviousMonthUrl('report/', $year, $month);
        // Get the names for this month
        $names = Helper::getNamesForMonth($formatted_month);
        return view('reports.show_month', compact('reports', 'names',
            'readable_month', 'next_month_url', 'previous_month_url'));
    }
}
