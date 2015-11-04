<?php

Route::get('/', function() {
    return view('homepage');
});

Route::resource('episode', 'EpisodeController',
    ['except' => ['index', 'show']]);
Route::resource('staffgroup', 'StaffgroupController',
    ['except' => ['show', 'destroy']]);
Route::resource('comment', 'CommentController',
    ['except' => ['show', 'destroy']]);
Route::resource('rawplan', 'RawplanController',
    ['except' => ['show', 'edit', 'update']]);

Route::get('report/{year}/{month}', 'ReportController@showMonth')
    ->where(['year' => '[0-9]+', 'month' => '[0-9]+']);
Route::get('report/{year}', 'ReportController@showYear')
    ->where(['year' => '[0-9]+']);

Route::resource('employee', 'EmployeeController');

Route::resource('personinfo', 'PersonInfoController');
Route::get('person/{number}', 'PersonInfoController@showEpisodes');

Route::get('month/{year}/{month}', 'MonthController@show')
    ->where(['year' => '[0-9]+', 'month' => '[0-9]+']);

/*
 * From here on, the routes are publically accessible.
 */

Route::get('anon/episodes/{hash}', 'PersonInfoController@anonEpisodes');
Route::post('anon/newHash', 'PersonInfoController@requestNewHashPerMail');
