<?php

use App\Services\Planparser;

// Set up some fake data
$data['people'] = "Ward, Layla
Palmer, Kenna
Pratt, Terry
Hooper, Clark
";

$data['shifts'] = "1\t2\t3\t4
a\tb\tc\td
A\tB\tC\tD
\t\t\t
";

test('the planparser can handle removed trailing whitespace', function () use (
    $data,
) {
    $p = new Planparser('2022-04', $data);
    expect($p)->toBeInstanceOf(Planparser::class);

    // The last newline should have been removed
    expect($p->rawNames)
        ->not()
        ->toBe($data['people']);
    expect($p->rawNames . "\n")->toBe($data['people']);

    // The trailing whitespace should have been removed
    expect($p->rawShifts)
        ->not()
        ->toBe($data['shifts']);
    expect($p->rawShifts . "\n\t\t\t\n")->toBe($data['shifts']);
});
