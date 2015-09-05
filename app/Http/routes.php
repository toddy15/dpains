<?php

Route::resource('episode', 'EpisodeController',
    ['except' => ['index', 'show']]);
