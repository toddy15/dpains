<?php

use App\Services\Planparser;

// Set up some fake data
$input_one_line['people'] = "Ward, Layla
Palmer, Kenna
Pratt, Terry
Hooper, Clark
";

$input_one_line['shifts'] = "1\t2\t3\t4
a\tb\tc\td
A\tB\tC\tD
\t\t\t
";

$input_three_lines['people'] = "Ward, Layla
Chefarzt, SpWB INT

Palmer, Kenna
Chefarzt-V, SpWB INT

Pratt, Terry
OA, SpWB INT

Hooper, Clark
FA

";

$input_three_lines['shifts'] = "1\t2\t3\t4
1\t2\t3\t4

a\tb\tc\td
a\tb\tc\td

A\tB\tC\tD
A\tB\tC\tD

\t\t\t
\t\t\t

";

$result['people'] = [
    'Ward, Layla',
    'Palmer, Kenna',
    'Pratt, Terry',
    'Hooper, Clark',
];

$result['shifts'] = ["1\t2\t3\t4", "a\tb\tc\td", "A\tB\tC\tD", "\t\t\t"];

test('the planparser removes trailing whitespace (1 line)', function () use (
    $input_one_line,
) {
    $p = new Planparser('2022-04', $input_one_line);
    expect($p)->toBeInstanceOf(Planparser::class);

    // The last newline should have been removed
    expect($p->rawNames)
        ->not()
        ->toBe($input_one_line['people']);
    expect($p->rawNames . "\n")->toBe($input_one_line['people']);

    // The trailing whitespace should have been removed
    expect($p->rawShifts)
        ->not()
        ->toBe($input_one_line['shifts']);
    expect($p->rawShifts . "\n\t\t\t\n")->toBe($input_one_line['shifts']);
});

test('the planparser removes trailing whitespace (3 lines)', function () use (
    $input_three_lines,
) {
    $p = new Planparser('2022-04', $input_three_lines);
    expect($p)->toBeInstanceOf(Planparser::class);

    // The last newline should have been removed
    expect($p->rawNames)
        ->not()
        ->toBe($input_three_lines['people']);
    expect($p->rawNames . "\n\n")->toBe($input_three_lines['people']);

    // The trailing whitespace should have been removed
    expect($p->rawShifts)
        ->not()
        ->toBe($input_three_lines['shifts']);
    expect($p->rawShifts . "\n\n\t\t\t\n\t\t\t\n\n")->toBe(
        $input_three_lines['shifts'],
    );
});

test(
    'the planparser adds empty lines for removed trailing whitespace (1 line)',
    function () use ($input_one_line, $result) {
        $p = new Planparser('2022-04', $input_one_line);

        expect($p->parsedNames)->toBe($result['people']);
        expect($p->parsedShifts)->toBe($result['shifts']);
    },
);

test(
    'the planparser adds empty lines for removed trailing whitespace (3 lines)',
    function () use ($input_three_lines, $result) {
        $p = new Planparser('2022-04', $input_three_lines);

        expect($p->parsedNames)->toBe($result['people']);
        expect($p->parsedShifts)->toBe($result['shifts']);
    },
);
