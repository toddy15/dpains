<?php

declare(strict_types=1);

use App\Models\Rawplan;
use App\Services\Helper;
use App\Services\Planparser;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(Tests\TestCase::class, LazilyRefreshDatabase::class)->in('Feature');

/**
 * Handle dynamic data for CSRF fields
 */
expect()->pipe('toMatchSnapshot', function (Closure $next) {
    if (is_string($this->value)) {
        $this->value = preg_replace(
            '/name="_token" value=".*"/',
            'name="_token" value="testtoken"',
            $this->value
        );
        $this->value = preg_replace(
            '/name="csrf-token" content=".*"/',
            'name="csrf-token" content="testtoken"',
            $this->value
        );
        $this->value = preg_replace(
            '/<p>Stand der Auswertung: .*/',
            '<p>Stand der Auswertung: Heute</p>',
            $this->value
        );
    }

    return $next();
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Loads a dataset for testing into the database via Planparser.
 *
 * The name format of the dataset is "YYYY-MM_description".
 *
 * @return void
 */
function loadDataset(string $dataset)
{
    $people = file_get_contents('tests/datasets/'.$dataset.'-people.txt');
    $shifts = file_get_contents('tests/datasets/'.$dataset.'-shifts.txt');
    $date = explode('_', $dataset)[0];

    Rawplan::create([
        'month' => $date,
        'people' => $people,
        'shifts' => $shifts,
        'anon_report' => false,
    ]);

    $p = new Planparser($date, $people, $shifts);
    $p->storeShiftsForPeople(new Helper);
}
