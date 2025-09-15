<?php

use App\Models\Episode;
use App\Services\Helper;
use Illuminate\Support\Carbon;
use Tests\Seeders\RefactorEpisodeSeeder;

it('returns the correct episodes', function (): void {
    // Set the testing date to 2025-09, so the expectations
    // with past and future employees work.
    Carbon::setTestNow(Carbon::createFromDate(2025, 9, 1));

    $this->seed(RefactorEpisodeSeeder::class);

    $helper = new Helper;
    $result = $helper->getPeopleForMonth('2025-09');
    $resultsArray = json_decode(json_encode($result), true);
    $episodes = Episode::hydrate($resultsArray);

    expect(count($episodes))->toBe(3);

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
