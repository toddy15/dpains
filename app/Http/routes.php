<?php

Route::resource('episode', 'EpisodeController',
    ['except' => ['index', 'show']]);
Route::resource('staffgroup', 'StaffgroupController',
    ['except' => ['show', 'destroy']]);
Route::resource('comment', 'CommentController',
    ['except' => ['show', 'destroy']]);

Route::get('person/{number}', 'PersonController@show');

Route::get('month/{month}', 'MonthController@show');
