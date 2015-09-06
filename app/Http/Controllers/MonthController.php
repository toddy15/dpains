<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MonthController extends Controller
{
    public function show($month)
    {
        return $month;
    }
}
