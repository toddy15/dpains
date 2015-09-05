<?php

Route::resource('episode', 'EpisodeController',
    ['except' => ['index', 'show']]);
Route::resource('staffgroup', 'StaffgroupController',
    ['except' => ['show', 'destroy']]);
