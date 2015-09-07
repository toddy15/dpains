<?php

Route::resource('episode', 'EpisodeController',
    ['except' => ['index', 'show']]);
Route::resource('staffgroup', 'StaffgroupController',
    ['except' => ['show', 'destroy']]);
Route::resource('comment', 'CommentController',
    ['except' => ['show', 'destroy']]);

Route::get('person/{number}', 'PersonController@show');

Route::get('month/{year}/{month}', 'MonthController@show')
    ->where(['year' => '[0-9]+', 'month' => '[0-9]+']);
