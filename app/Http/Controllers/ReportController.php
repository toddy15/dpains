<?php

namespace App\Http\Controllers;

use App\Dpains\Analyzer;
use App\Dpains\Reporter;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function show($year, $month)
    {
        $reporter = new Reporter();
        $formatted_month = $reporter->validateAndFormatDate($year, $month);
        return $reporter->getNamesForMonth($formatted_month);
    }
}
