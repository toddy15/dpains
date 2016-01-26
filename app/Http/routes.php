<?php

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
//Route::get('auth/register', 'Auth\AuthController@getRegister');
//Route::post('auth/register', 'Auth\AuthController@postRegister');
//Route::get('auth/register', ['middleware' => 'auth', 'uses' => 'Auth\AuthController@getRegister']);
//Route::post('auth/register', ['middleware' => 'auth', 'uses' => 'Auth\AuthController@postRegister']);

// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

/*
 * These routes are only accessible by authenticated users.
 */
Route::group(['middleware' => 'auth'], function () {
    Route::resource('episode', 'EpisodeController',
        ['except' => ['index', 'show']]);
    Route::resource('staffgroup', 'StaffgroupController',
        ['except' => ['show', 'destroy']]);
    Route::resource('comment', 'CommentController',
        ['except' => ['show', 'destroy']]);
    Route::resource('rawplan', 'RawplanController',
        ['except' => ['show', 'edit', 'update']]);
    Route::put('rawplan/setAnonReportMonth', 'RawplanController@setAnonReportMonth');
    Route::resource('employee', 'EmployeeController',
        ['except' => ['create', 'store', 'show', 'destroy']]);
    Route::get('employee/{id}/episodes', 'EmployeeController@showEpisodes');
    Route::get('employee/month/{year}/{month}', 'EmployeeController@showMonth')
        ->where(['year' => '[0-9]+', 'month' => '[0-9]+']);
    Route::get('employee/vk/{which_vk}/{year}', 'EmployeeController@showVKForYear')
        ->where(['year' => '[0-9]+']);

    Route::get('report/{year}/{month}', 'ReportController@showMonth')
        ->where(['year' => '[0-9]+', 'month' => '[0-9]+']);
    Route::get('report/{year}', 'ReportController@showYear')
        ->where(['year' => '[0-9]+']);

    Route::get('backup', 'BackupController@index');
    Route::get('backup/download', 'BackupController@download');
    Route::post('backup/restore', 'BackupController@restore');
});

/*
 * From here on, the routes are accessible by anybody.
 */

Route::get('/{hash?}', 'AnonController@homepage');
Route::get('anon/logout/{hash}', 'AnonController@logout');
Route::get('anon/episodes/{hash}', 'AnonController@showEpisodes');
Route::post('anon/newHash', 'AnonController@requestNewHashPerMail');
Route::get('anon/{year}/{hash}', 'AnonController@showYear')
    ->where(['year' => '[0-9]+']);
