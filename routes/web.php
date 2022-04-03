<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

/*
 * These routes are only accessible by authenticated users.
 */
Route::group(['middleware' => 'auth'], function () {
    Route::resource(
        'episode',
        'App\Http\Controllers\EpisodeController',
        ['except' => ['index', 'show']]
    );
    Route::resource(
        'staffgroup',
        'App\Http\Controllers\StaffgroupController',
        ['except' => ['show', 'destroy']]
    );
    Route::resource(
        'comment',
        'App\Http\Controllers\CommentController',
        ['except' => ['show', 'destroy']]
    );
    Route::resource(
        'rawplan',
        'App\Http\Controllers\RawplanController',
        ['except' => ['show', 'edit', 'update']]
    );
    Route::resource(
        'due_shift',
        'App\Http\Controllers\DueShiftController',
        ['except' => ['show', 'destroy']]
    );
    Route::put('rawplan/setAnonReportMonth', 'App\Http\Controllers\RawplanController@setAnonReportMonth');
    Route::resource(
        'employee',
        'App\Http\Controllers\EmployeeController',
        ['except' => ['create', 'store', 'show', 'destroy']]
    );
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