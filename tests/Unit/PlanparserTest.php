<?php

use App\Services\Planparser;

// Set up some fake data
$input['people'] = "Ward, Layla
Palmer, Kenna
Pratt, Terry
Hooper, Clark
";

$input['shifts'] = "1\t2\t3\t4
a\tb\tc\td
A\tB\tC\tD
\t\t\t
";

$result['people'] = [
    'Ward, Layla',
    'Palmer, Kenna',
    'Pratt, Terry',
    'Hooper, Clark',
];

$result['shifts'] = ["1\t2\t3\t4", "a\tb\tc\td", "A\tB\tC\tD", "\t\t\t"];

test('the planparser removes trailing whitespace', function () use ($input) {
    $p = new Planparser('2022-04', $input);
    expect($p)->toBeInstanceOf(Planparser::class);

    // The last newline should have been removed
    expect($p->rawNames)
        ->not()
        ->toBe($input['people']);
    expect($p->rawNames . "\n")->toBe($input['people']);

    // The trailing whitespace should have been removed
    expect($p->rawShifts)
        ->not()
        ->toBe($input['shifts']);
    expect($p->rawShifts . "\n\t\t\t\n")->toBe($input['shifts']);
});

test(
    'the planparser adds empty lines for removed trailing whitespace',
    function () use ($input, $result) {
        $p = new Planparser('2022-04', $input);

        expect($p->parsedNames)->toBe($result['people']);
        expect($p->parsedShifts)->toBe($result['shifts']);
    },
);
