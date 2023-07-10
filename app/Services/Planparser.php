<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Planparser
{
    public string $rawNames = '';

    public string $rawShifts = '';

    /** @var array<int, string> */
    public array $parsedNames = [];

    /** @var array<int, array<int, string>> */
    public array $parsedShifts = [];

    public int $lines_per_person = 1;

    /**
     * Planparser constructor.
     *
     * If called with a rawInput array, this is always $request->all().
     */
    public function __construct(
        public string $formattedMonth,
        public string $people,
        public string $shifts,
    ) {
        // @TODO: Refactor to use people and shifts directly
        // Trim the input's newlines
        $this->rawNames = trim($people, "\r\n");
        $this->rawShifts = trim($shifts, "\r\n");

        $this->parseNames();
        $this->parseShifts();
    }

    public function parseNames(): void
    {
        $person_lines = explode("\n", $this->rawNames);

        // Check number of lines per person
        // The first line should always be a person's name
        // The second line might be another person, then
        // the number of lines per person is 1.
        // Otherwise, if the second line is an attribute,
        // the lines per person are 3.
        $second_line = trim($person_lines[1] ?? $person_lines[0]);
        $attributes = [
            'Chefarzt',
            'Chefarzt-V',
            'OA',
            'ASS/FA',
            'FA-ÄD_1',
            'FA',
            'Ass-Arzt',
        ];
        $found_attribute = false;
        foreach ($attributes as $attribute) {
            if (str_contains($second_line, $attribute)) {
                $found_attribute = true;
                break;
            }
        }
        // The old plans have all attributes stripped, so
        // check for an empty line
        if ($found_attribute or $second_line == '') {
            $this->lines_per_person = 3;
        }

        $line_counter = 0;
        foreach ($person_lines as $person_line) {
            if ($line_counter % $this->lines_per_person == 0) {
                // Trim whitespace, so that the regexp matches dates at the end.
                $person_line = trim($person_line);
                // Remove comma, space and end dates from names.
                $person_line = preg_replace(
                    "/\s*,?\s*[0-9.]+$/",
                    '',
                    $person_line,
                );
                // Skip empty lines
                if (empty($person_line)) {
                    continue;
                }
                // Finally, add the name to list.
                $this->parsedNames[] = trim($person_line);
            }
            $line_counter++;
        }
    }

    public function parseShifts(): void
    {
        $plan_lines = explode("\n", $this->rawShifts);

        // Cycle through all names and get the shifts for that person
        foreach ($this->parsedNames as $index => $name) {
            if ($this->lines_per_person == 3) {
                $plan_index = 3 * $index + 1;
            } else {
                $plan_index = $index;
            }
            // Explode shift lines and trim single shifts
            $result = array_map(
                'trim',
                explode("\t", $plan_lines[$plan_index] ?? ''),
            );
            $this->parsedShifts[$index] = $result;
        }
    }

    public function storeShiftsForPeople(): void
    {
        $helper = new Helper();
        // Clean all previously parsed results.
        DB::table('analyzed_months')
            ->where('month', $this->formattedMonth)
            ->delete();
        // Get an array with the unique person's number and name in this episode.
        $expected_names = $helper->getNamesForMonth($this->formattedMonth);
        $database_rows = [];
        foreach ($this->parsedNames as $id => $name) {
            $person_id = array_search($name, $expected_names);
            $shifts = $this->calculateShifts($this->parsedShifts[$id]);
            if (! is_array($shifts)) {
                // Clean all previously parsed results.
                DB::table('analyzed_months')
                    ->where('month', $this->formattedMonth)
                    ->delete();

                return;
            }
            $database_rows[] = [
                'month' => $this->formattedMonth,
                'employee_id' => $person_id,
                'nights' => $shifts['nights'],
                'nefs' => $shifts['nefs'],
                'bus' => $shifts['bus'],
                'cons' => $shifts['cons'],
            ];
        }
        DB::table('analyzed_months')->insert($database_rows);
    }

    /**
     * @param array<int, string> $shifts
     * @return array{nights: int, nefs: int, bus: int, cons: int}
     */
    public function calculateShifts(array $shifts): array|string
    {
        // @TODO: Do not hardcode.
        $nights = ['0r', 'D0', 'D1', 'i30', 'i36', 'n2'];
        $nefs = ['n1', 'n2', 'n3'];
        $bus = ['BU'];
        $cons = ['Con'];
        $ignored = [
            '1',
            '2',
            '3',
            'st',
            '25',
            '26',
            '27',
            'D2',
            'i28',
            'i29',
            'i33',
            'i35',
            'dt0',
            'dt1',
            'FÜ',
            'BF',
            'TZ',
            'MS',
            'EZ',
            'U',
            'FBi*',
            'FBe*',
            'K',
            'KO',
            'Kol',
            'KP',
            'KK',
            'KÜ',
            'ZU',
            'BR',
            'F.',
            '',
            'DB',
            'Ve',
            'US',
            '--',
            'BV',
            'PZU = Platzhalt',
            'B4',
            'B3',
            'B2',
            'B1',
            'SU',
            'ir28',
            'ir29',
            'FSI',
            'TPB',
            'TxB',
            'FZ',
            'Avk',
            'FS',
            'USB',
            '????',
            'RWe',
            'F',
            'S',
            'N',
            'S2W',
            'iOA',
        ];
        $night_counter = 0;
        $nef_counter = 0;
        $bu_counter = 0;
        $con_counter = 0;
        foreach ($shifts as $shift) {
            $unknown_shift = true;
            if (in_array($shift, $nights)) {
                $night_counter++;
                $unknown_shift = false;
            }
            if (in_array($shift, $nefs)) {
                $nef_counter++;
                $unknown_shift = false;
            }
            if (in_array($shift, $bus)) {
                $bu_counter++;
                $unknown_shift = false;
            }
            if (in_array($shift, $cons)) {
                $con_counter++;
                $unknown_shift = false;
            }
            if (in_array($shift, $ignored)) {
                $unknown_shift = false;
            }
            if ($unknown_shift) {
                return 'Unbekannte Dienstart: '.$shift;
            }
        }

        return [
            'nights' => $night_counter,
            'nefs' => $nef_counter,
            'bus' => $bu_counter,
            'cons' => $con_counter,
        ];
    }

    /**
     * This method returns all error messages as an array.
     * If there are no errors, the array is emtpy.
     *
     * @return array<int, string>
     */
    public function validatePeople(): array
    {
        $helper = new Helper();
        $result = [];
        // Get all people which are expected in this month.
        $expected_people = $helper->getNamesForMonth($this->formattedMonth);
        // Check that all expected people have been found.
        $more_expected = array_diff($expected_people, $this->parsedNames);
        if ($more_expected) {
            $result[] =
                'Die folgenden Mitarbeiter werden im Monat '.
                $this->formattedMonth.
                ' erwartet, aber nicht gefunden: '.
                implode('; ', $more_expected);
        }
        // Check that not more than the expected people have been found.
        $more_found = array_diff($this->parsedNames, $expected_people);
        if ($more_found) {
            $result[] =
                'Die folgenden Mitarbeiter werden im Monat '.
                $this->formattedMonth.
                ' nicht erwartet, aber gefunden: '.
                implode('; ', $more_found);
        }

        return $result;
    }

    /**
     * This method returns all error messages as an array.
     * If there are no errors, the array is emtpy.
     *
     * @return array<int, string>
     */
    public function validateShifts(): array
    {
        $result = [];
        // Count the days in the line
        $submitted_days = count($this->parsedShifts[0]);
        // The submitted days must be exactly one month.
        // so check that the next day is the first of a month.
        if ($submitted_days > 31) {
            $result[] =
                $this->formattedMonth.
                ': Es wurden mehr als 31 Tage in den Schichten gefunden.';
        }
        $end_day = Carbon::parse($this->formattedMonth)
            ->addDays($submitted_days)
            ->isoFormat('D');
        if ($end_day !== '1') {
            $result[] =
                $this->formattedMonth.
                ': Die Anzahl der Tage in den Schichten stimmt nicht mit der Anzahl der Tage des Monats überein.';
        }
        // Do not error out if there's one line break appended.
        $shift_lines = count(explode("\n", $this->rawShifts));
        $people_lines = count(explode("\n", $this->rawNames));
        if (
            $shift_lines !== $people_lines and
            $shift_lines - 1 !== $people_lines
        ) {
            $result[] =
                $this->formattedMonth.
                ': Die Anzahl der Zeilen in den Schichten muss entweder eine oder drei pro Mitarbeiter sein.';
        }
        // Try parsing all shifts to detect unknown shifts.
        foreach ($this->parsedNames as $index => $name) {
            $shifts = $this->calculateShifts($this->parsedShifts[$index]);
            if (! is_array($shifts)) {
                // Add the error message from calculateShifts().
                $result[] = $this->formattedMonth.': '.$shifts;
            }
        }

        return $result;
    }
}
