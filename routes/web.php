<?php

use App\Http\Controllers\AnonController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DueShiftController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeEpisodeController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\PastEmployeeController;
use App\Http\Controllers\RawplanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaffgroupController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::middleware([Authenticate::class])->group(function () {
    Route::resource('episodes', EpisodeController::class)->except([
        'index',
        'show',
    ]);
    Route::resource('staffgroups', StaffgroupController::class)->except([
        'show',
        'destroy',
    ]);
    Route::resource('comments', CommentController::class)->except([
        'show',
        'destroy',
    ]);
    Route::resource('rawplans', RawplanController::class)->except([
        'show',
        'edit',
        'update',
    ]);
    Route::resource('due_shifts', DueShiftController::class)->except([
        'show',
        'destroy',
    ]);
    Route::put('rawplans/setAnonReportMonth', [RawplanController::class, 'setAnonReportMonth'])
        ->name('rawplans.setAnonReportMonth');
    Route::resource('employees', EmployeeController::class)->except([
        'create',
        'store',
        'show',
        'destroy',
    ]);
    Route::resource('employees.episodes', EmployeeEpisodeController::class)
        ->only(['index']);
    Route::get('employees/month/{year}/{month}', [EmployeeController::class, 'showMonth'])
        ->where(['year' => '[0-9]+', 'month' => '[0-9]+']);
    Route::get(
        'employees/vk/{which_vk}/{year}',
        [EmployeeController::class, 'showVKForYear'],
    )->where(['year' => '[0-9]+']);
    Route::get(
        'employees/vk/{which_vk}',
        [EmployeeController::class, 'showCurrentVKForYear'],
    );
    Route::resource('employees/past', PastEmployeeController::class)->only([
        'index',
    ]);

    Route::get(
        'report/{year}/{month}',
        [ReportController::class, 'showMonth'],
    )->where(['year' => '[0-9]+', 'month' => '[0-9]+']);
    Route::get(
        'report/{year}',
        [ReportController::class, 'showYear'],
    )
        ->where(['year' => '[0-9]+'])
        ->name('reports.showYear');
    Route::get(
        'report',
        [ReportController::class, 'showCurrentYear'],
    )
        ->name('reports.showCurrentYear');
    Route::get(
        'report/buandcon/{year}',
        [ReportController::class, 'showBuAndCon'],
    )->where(['year' => '[0-9]+']);
    Route::get(
        'report/buandcon',
        [ReportController::class, 'showCurrentBuAndCon'],
    )
        ->name('reports.showCurrentBuAndCon');
    Route::get(
        'report/refresh',
        [ReportController::class, 'refresh'],
    );
});

/*
 * From here on, the routes are accessible by anybody.
 */

Route::get('/{hash?}', [AnonController::class, 'homepage'])->name(
    'homepage',
);
Route::get('anon/logout/{hash}', [AnonController::class, 'logout']);
Route::get(
    'anon/episodes/{hash}',
    [AnonController::class, 'showEpisodes'],
);
Route::post(
    'anon/newHash',
    [AnonController::class, 'requestNewHashPerMail'],
)->name('anon.newHash');
Route::get('anon/{year}/{hash}', [AnonController::class, 'showYear'])
    ->where(['year' => '[0-9]+'])
    ->name('anon.showYear');
Route::get('anon/{hash}', [AnonController::class, 'showCurrentYear'])
    ->name('anon.showCurrentYear');
