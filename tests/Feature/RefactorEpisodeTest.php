<?php

use App\Models\Episode;
use App\Services\Helper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\Seeders\RefactorEpisodeSeeder;

it('returns the correct episodes from getPeopleForMonth()', function (): void {
    // Set the testing date to 2025-09, so the expectations
    // with past and future employees work.
    Carbon::setTestNow(Carbon::createFromDate(2025, 9, 1));

    $this->seed(RefactorEpisodeSeeder::class);

    $helper = new Helper;
    $result = $helper->getPeopleForMonth('2025-09');

    expect($result)->toHaveCount(3);

    $expected_names = [
        'Person, Single episode',
        'Person, Multiple episodes',
        'Person, Terminated and started again',
    ];
    $names = $result->map(function ($episode) {
        return $episode->name;
    });
    foreach ($expected_names as $name) {
        expect($names)->toContain($name);
    }

    // Reset testing time
    Carbon::setTestNow();
});

it('returns the correct episodes from getEpisodesForMonth()', function (): void {
    // Set the testing date to 2025-09, so the expectations
    // with past and future employees work.
    Carbon::setTestNow(Carbon::createFromDate(2025, 9, 1));

    $this->seed(RefactorEpisodeSeeder::class);

    $helper = new Helper;
    $episodes = $helper->getEpisodesForMonth('2025-09');

    expect($episodes)->toBeInstanceOf(Collection::class);
    expect($episodes)->toHaveCount(3);

    $expected_names = [
        'Person, Single episode',
        'Person, Multiple episodes',
        'Person, Terminated and started again',
    ];
    $names = $episodes->map(function (Episode $episode) {
        return $episode->name;
    });
    foreach ($expected_names as $name) {
        expect($names)->toContain($name);
    }

    // Reset testing time
    Carbon::setTestNow();
});
