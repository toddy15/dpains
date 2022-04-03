<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DueShiftController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\RawplanController;
use App\Http\Controllers\StaffgroupController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::middleware([Authenticate::class])->group(function () {
    Route::resource('episode', EpisodeController::class)
        ->except(['index', 'show']);
    Route::resource('staffgroup', StaffgroupController::class)
        ->except(['show', 'destroy']);
    Route::resource('comment', CommentController::class)
        ->except(['show', 'destroy']);
    Route::resource('rawplan', RawplanController::class)
        ->except(['show', 'edit', 'update']);
    Route::resource('due_shift', DueShiftController::class)
        ->except(['show', 'destroy']);
    Route::put('rawplan/setAnonReportMonth', [RawplanController::class, 'setAnonReportMonth']);
    Route::resource('employee', EmployeeController::class)
        ->except(['create', 'store', 'show', 'destroy']);
    Route::get('employee/{id}/episodes', 'App\Http\Controllers\EmployeeController@showEpisodes');
    Route::get('employee/month/{year}/{month}', 'App\Http\Controllers\EmployeeController@showMonth')
        ->where(['year' => '[0-9]+', 'month' => '[0-9]+']);
    Route::get('employee/vk/{which_vk}/{year}', 'App\Http\Controllers\EmployeeController@showVKForYear')
        ->where(['year' => '[0-9]+']);
    Route::get('employee/past', 'App\Http\Controllers\EmployeeController@showPastEmployees');

    Route::get('report/{year}/{month}', 'App\Http\Controllers\ReportController@showMonth')
        ->where(['year' => '[0-9]+', 'month' => '[0-9]+']);
    Route::get('report/{year}', 'App\Http\Controllers\ReportController@showYear')
        ->where(['year' => '[0-9]+']);
    Route::get('report/buandcon/{year}', 'App\Http\Controllers\ReportController@showBuAndCon')
        ->where(['year' => '[0-9]+']);
    Route::get('report/refresh', 'App\Http\Controllers\ReportController@refresh');
});

/*
 * From here on, the routes are accessible by anybody.
 */

Route::get('/{hash?}', 'App\Http\Controllers\AnonController@homepage');
Route::get('anon/logout/{hash}', 'App\Http\Controllers\AnonController@logout');
Route::get('anon/episodes/{hash}', 'App\Http\Controllers\AnonController@showEpisodes');
Route::post('anon/newHash', 'App\Http\Controllers\AnonController@requestNewHashPerMail');
Route::get('anon/{year}/{hash}', 'App\Http\Controllers\AnonController@showYear')
    ->where(['year' => '[0-9]+']);
