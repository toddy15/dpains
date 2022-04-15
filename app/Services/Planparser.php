<?php

namespace App\Services;

use App\Models\Rawplan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Planparser
{
    public string $rawNames = '';
    public string $rawShifts = '';
    public array $parsedNames = [];
    public array $parsedShifts = [];
    public int $lines_per_person = 1;

    /**
     * Planparser constructor.
     *
     * If called with a rawInput array, this is always $request->all().
     * If there is no rawInput array, fetch the data from the DB.
     *
     * @param string $formattedMonth
     * @param array|null $rawInput
     */
    public function __construct(
        public string $formattedMonth,
        array $rawInput = null,
    ) {
        // Ensure that there is data for names and shifts.
        if (!empty($rawInput)) {
            $this->rawNames = $rawInput['people'];
            $this->rawShifts = $rawInput['shifts'];
        } else {
            $rawplan = Rawplan::where('month', $this->formattedMonth)->first();
            $this->rawNames = $rawplan->people;
            $this->rawShifts = $rawplan->shifts;
        }

        // Trim the input, as Laravel does this by default now
        $this->rawNames = trim($this->rawNames);
        $this->rawShifts = trim($this->rawShifts);

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
        $second_line = trim($person_lines[1]);
        $attributes = [
            'Chefarzt',
            'Chefarzt-V',
            'OA',
            'ASS\/FA',
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
        if ($found_attribute) {
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
        $number_of_plan_lines = count($plan_lines);
        foreach ($this->parsedNames as $index => $name) {
            if ($this->lines_per_person == 3) {
                $plan_index = 3 * $index + 1;
            } else {
                $plan_index = $index;
            }
            // If there is data in the line, use it.
            if ($plan_index < $number_of_plan_lines) {
                // Remove line endings, but not tabs.
                $result = trim($plan_lines[$plan_index], "\r\n");
            } else {
                // The whitespace line has been removed,
                // so add tabs for empty shifts.
                // Count the days in the first line
                $submitted_days = count(explode("\t", $plan_lines[0]));
                $result = str_repeat("\t", $submitted_days - 1);
            }
            $this->parsedShifts[$index] = explode("\t", $result);
        }
    }

    public function storeShiftsForPeople()
    {
        // Clean all previously parsed results.
        DB::table('analyzed_months')
            ->where('month', $this->formattedMonth)
            ->delete();
        // Get an array with the unique person's number and name in this episode.
        $expected_names = Helper::getNamesForMonth($this->formattedMonth);
        $database_rows = [];
        foreach ($this->parsedNames as $id => $name) {
            $person_id = array_search($name, $expected_names);
            $shifts = $this->calculateShifts($this->parsedShifts[$id]);
            if (!is_array($shifts)) {
                // Clean all previously parsed results.
                DB::table('analyzed_months')
                    ->where('month', $this->formattedMonth)
                    ->delete();
                // Return the error message from calculateShifts().
                return $shifts;
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

    public function calculateShifts($shifts)
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
                return 'Unbekannte Dienstart: ' . $shift;
            }
        }

        return [
            'nights' => $night_counter,
            'nefs' => $nef_counter,
            'bus' => $bu_counter,
            'cons' => $con_counter,
        ];
    }

    public function validatePeople()
    {
        $result = [];
        // Get all people which are expected in this month.
        $expected_people = Helper::getNamesForMonth($this->formattedMonth);
        // Check that all expected people have been found.
        $more_expected = array_diff($expected_people, $this->parsedNames);
        if ($more_expected) {
            $result[] =
                'Die folgenden Mitarbeiter werden im Monat ' .
                $this->formattedMonth .
                ' erwartet, aber nicht gefunden: ' .
                join('; ', $more_expected);
        }
        // Check that not more than the expected people have been found.
        $more_found = array_diff($this->parsedNames, $expected_people);
        if ($more_found) {
            $result[] =
                'Die folgenden Mitarbeiter werden im Monat ' .
                $this->formattedMonth .
                ' nicht erwartet, aber gefunden: ' .
                join('; ', $more_found);
        }

        return $result;
    }

    public function validateShifts()
    {
        $result = [];
        // Count the days in the line
        $submitted_days = count($this->parsedShifts[0]);
        // The submitted days must be exactly one month.
        // so check that the next day is the first of a month.
        if ($submitted_days > 31) {
            $result[] =
                $this->formattedMonth .
                ': Es wurden mehr als 31 Tage in den Schichten gefunden.';
        }
        $end_day = Carbon::create($this->formattedMonth . '-01')
            ->addDays($submitted_days)
            ->isoFormat('D');
        if ($end_day !== '1') {
            $result[] =
                $this->formattedMonth .
                ': Die Anzahl der Tage in den Schichten stimmt nicht mit der Anzahl der Tage des Monats überein.';
        }
        // Avoid a division by zero
        if (count($this->parsedNames) == 0) {
            return $result;
        }
        // Try parsing all shifts to detect unknown shifts.
        foreach ($this->parsedNames as $index => $name) {
            $shifts = $this->calculateShifts($this->parsedShifts[$index]);
            if (!is_array($shifts)) {
                // Add the error message from calculateShifts().
                $result[] = $this->formattedMonth . ': ' . $shifts;
            }
        }

        return $result;
    }
}
