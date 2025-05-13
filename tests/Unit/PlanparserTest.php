<?php

use App\Services\Planparser;

// Set up some fake data
$input_one_line_people = 'Ward, Layla
Palmer, Kenna
Pratt, Terry
Hooper, Clark
';

$input_one_line_shifts = "1\t2\t3\t4
a\tb\tc\td
A\tB\tC\tD
\t\t\t
";

$input_one_line_whitespace_shifts = "\t\t\t4
a\tb\tc\td
A\tB\tC\tD
\t\t\t
";

$input_three_lines_people = 'Ward, Layla
Chefarzt, SpWB INT

Palmer, Kenna
Chefarzt-V, SpWB INT

Pratt, Terry
OA, SpWB INT

Hooper, Clark
FA

';

$input_three_lines_shifts = "1p\t2p\t3p\t4p
1\t2\t3\t4

ap\tbp\tcp\tdp
a\tb\tc\td

Ap\tBp\tCp\tDp
A\tB\tC\tD

\t\t\t
\t\t\t

";

$input_three_lines_whitespace_shifts = "\t\t\t4p
\t\t\t4

ap\tbp\tcp\tdp
a\tb\tc\td

Ap\tBp\tCp\tDp
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

$result['shifts'] = [
    ['1', '2', '3', '4'],
    ['a', 'b', 'c', 'd'],
    ['A', 'B', 'C', 'D'],
    ['', '', '', ''],
];

$result_whitespace['shifts'] = [
    ['', '', '', '4'],
    ['a', 'b', 'c', 'd'],
    ['A', 'B', 'C', 'D'],
    ['', '', '', ''],
];

it('removes trailing whitespace (1 line)', function () use (
    $input_one_line_people,
    $input_one_line_shifts,
) {
    $p = new Planparser(
        '2022-04',
        $input_one_line_people,
        $input_one_line_shifts,
    );
    expect($p)->toBeInstanceOf(Planparser::class);

    // The last newline should have been removed
    expect($p->rawNames)
        ->not()
        ->toBe($input_one_line_people);
    expect($p->rawNames."\n")->toBe($input_one_line_people);

    // The last newline should have been removed
    expect($p->rawShifts)
        ->not()
        ->toBe($input_one_line_shifts);
    expect($p->rawShifts."\n")->toBe($input_one_line_shifts);
});

it('removes trailing whitespace (3 lines)', function () use (
    $input_three_lines_people,
    $input_three_lines_shifts,
) {
    $p = new Planparser(
        '2022-04',
        $input_three_lines_people,
        $input_three_lines_shifts,
    );
    expect($p)->toBeInstanceOf(Planparser::class);

    // The last newline should have been removed
    expect($p->rawNames)
        ->not()
        ->toBe($input_three_lines_people);
    expect($p->rawNames."\n\n")->toBe($input_three_lines_people);

    // The last newline should have been removed
    expect($p->rawShifts)
        ->not()
        ->toBe($input_three_lines_shifts);
    expect($p->rawShifts."\n\n")->toBe($input_three_lines_shifts);
});

it(
    'adds empty lines for removed trailing whitespace (1 line)',
    function () use ($input_one_line_people, $input_one_line_shifts, $result) {
        $p = new Planparser(
            '2022-04',
            $input_one_line_people,
            $input_one_line_shifts,
        );

        expect($p->parsedNames)->toBe($result['people']);
        expect($p->parsedShifts)->toBe($result['shifts']);
    },
);

it(
    'adds empty lines for removed trailing whitespace (3 lines)',
    function () use (
        $input_three_lines_people,
        $input_three_lines_shifts,
        $result,
    ) {
        $p = new Planparser(
            '2022-04',
            $input_three_lines_people,
            $input_three_lines_shifts,
        );

        expect($p->parsedNames)->toBe($result['people']);
        expect($p->parsedShifts)->toBe($result['shifts']);
    },
);

it('returns planned shifts (1 line)', function () use (
    $input_one_line_people,
    $input_one_line_shifts,
    $result,
) {
    $p = new Planparser(
        '2022-04',
        $input_one_line_people,
        $input_one_line_shifts,
    );

    expect($p->parsedNames)->toBe($result['people']);
    expect($p->parsedShifts)->toBe($result['shifts']);
});

it('returns worked shifts (3 lines)', function () use (
    $input_three_lines_people,
    $input_three_lines_shifts,
    $result,
) {
    $p = new Planparser(
        '2022-04',
        $input_three_lines_people,
        $input_three_lines_shifts,
    );

    expect($p->parsedNames)->toBe($result['people']);
    expect($p->parsedShifts)->toBe($result['shifts']);
});

it('does not trim whitespace at the beginning (1 line)', function () use (
    $input_one_line_people,
    $input_one_line_whitespace_shifts,
    $result,
    $result_whitespace,
) {
    $p = new Planparser(
        '2022-04',
        $input_one_line_people,
        $input_one_line_whitespace_shifts,
    );

    expect($p->parsedNames)->toBe($result['people']);
    expect($p->parsedShifts)->toBe($result_whitespace['shifts']);
});

it('does not trim whitespace at the beginning (3 lines)', function () use (
    $input_three_lines_people,
    $input_three_lines_whitespace_shifts,
    $result,
    $result_whitespace,
) {
    $p = new Planparser(
        '2022-04',
        $input_three_lines_people,
        $input_three_lines_whitespace_shifts,
    );

    expect($p->parsedNames)->toBe($result['people']);
    expect($p->parsedShifts)->toBe($result_whitespace['shifts']);
});
