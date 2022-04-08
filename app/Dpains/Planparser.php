<?php

declare(strict_types=1);

namespace App\Dpains;

use App\Models\Rawplan;
use Illuminate\Support\Facades\DB;

class Planparser
{
    public $rawNames = [];
    public $rawShifts = [];
    public $parsedNames = [];
    public $parsedShifts = [];

    /**
     * Planparser constructor.
     *
     * If called with a rawInput array, this is always $request->all().
     * If there is no rawInput array, fetch the data from the DB.
     *
     * @param $formattedMonth
     * @param null $rawInput
     */
    public function __construct(public $formattedMonth, $rawInput = null)
    {
        // Ensure that there is data for names and shifts.
        if (! empty($rawInput)) {
            $this->rawNames = trim($rawInput['people'], "\r\n");
            $this->rawShifts = trim($rawInput['shifts'], "\r\n");
        } else {
            $rawplan = Rawplan::where('month', $this->formattedMonth)->first();
            $this->rawNames = $rawplan->people;
            $this->rawShifts = $rawplan->shifts;
        }
        $this->parseNames();
        $this->parseShifts();
    }

    public function parseNames()
    {
        $this->parsedNames = [];
        $person_lines = explode("\n", $this->rawNames);
        foreach ($person_lines as $person_line) {
            // Remove whitespace.
            $person_line = trim($person_line);
            // Remove skills
            // This needs to happen before removal of the date, because
            // otherwise the skill 'FA-ÄD_1' will be truncated.
            $skills = [
                '/Chefarzt-V/', '/Chefarzt/', '/OA/', '/ASS\/FA/',
                '/FA-ÄD_1/', '/FA/', '/Ass-Arzt/', '/ITS MED/',
                '/xITS Pflege/', '/SpWB INT/', '/"weiblich"/',
            ];
            $person_line = preg_replace($skills, '', $person_line);
            // Remove comma, space and end dates from names.
            $person_line = preg_replace("/\s*,?\s*[0-9.]+$/", '', $person_line);
            // If the line only consists of whitespace and comma, it
            // was a skills line, not a name. Remove it.
            if (preg_match("/^[, ]+$/", $person_line)) {
                continue;
            }
            // Skip empty lines
            if (strlen($person_line) == 0) {
                continue;
            }
            // Finally, add the name to list.
            $this->parsedNames[] = $person_line;
        }
    }

    public function parseShifts()
    {
        $this->parsedShifts = [];
        $plan_lines = explode("\n", $this->rawShifts);
        // Avoid a division by zero
        if (count($this->parsedNames) == 0) {
            return;
        }
        // Determine if there are 1 or 3 lines per person.
        $lines_per_person = count($plan_lines) / count($this->parsedNames);
        // This id is the index of the $this->names array. It has
        // nothing to do with the person number in the database.
        $parsed_person_id = 0;
        // This counter is for the current line of a person
        // and cycles between 1 and 3.
        $current_line_count = 1;
        // Set up arrays for the first line (plan) and the
        // second line (actually worked)
        $plan = [];
        $work = [];
        foreach ($plan_lines as $plan_line) {
            // Remove line endings, but not tabs.
            $plan_line = trim($plan_line, "\n\r");
            // Parse the days of a line
            switch ($current_line_count) {
                case 1:
                    $plan[$parsed_person_id] = explode("\t", $plan_line);

                    break;
                case 2:
                    $work[$parsed_person_id] = explode("\t", $plan_line);

                    break;
                case 3:
                    // Overwork hours, simply to be discarded.
                    break;
            }
            // Increment counter and check for cycling.
            $current_line_count++;
            if ($current_line_count > $lines_per_person) {
                $current_line_count = 1;
                $parsed_person_id++;
            }
        }
        // The relevant shifts for a person are either in the plan array
        // (if there is no second line yet) or in the work array (if
        // there are three lines per person). Determine which to use for
        // the analyzing.
        if ($lines_per_person == 1) {
            $this->parsedShifts = $plan;
        } elseif ($lines_per_person == 3) {
            $this->parsedShifts = $work;
        }
    }

    public function storeShiftsForPeople()
    {
        // Clean all previously parsed results.
        DB::table('analyzed_months')->where('month', $this->formattedMonth)->delete();
        // Get an array with the unique person's number and name in this episode.
        $expected_names = Helper::getNamesForMonth($this->formattedMonth);
        $database_rows = [];
        foreach ($this->parsedNames as $id => $name) {
            $person_id = array_search($name, $expected_names);
            $shifts = $this->calculateShifts($this->parsedShifts[$id]);
            if (! is_array($shifts)) {
                // Clean all previously parsed results.
                DB::table('analyzed_months')->where('month', $this->formattedMonth)->delete();
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
        $ignored = ['1', '2', '3', 'st', '25', '26', '27', 'D2', 'i28', 'i29', 'i33', 'i35', 'dt0', 'dt1',
            'FÜ', 'BF', 'TZ', 'MS', 'EZ', 'U', 'FBi*', 'FBe*', 'K', 'KO', 'Kol', 'KP', 'KK',
            'KÜ', 'ZU', 'BR', 'F.', '', 'DB', 'Ve', 'US', '--', 'BV', 'PZU = Platzhalt',
            'B4', 'B3', 'B2', 'B1', 'SU', 'ir28', 'ir29', 'FSI', 'TPB', 'TxB',
            'FZ', 'Avk', 'FS', 'USB', '????', 'RWe', 'F', 'S', 'N', 'S2W', 'iOA',
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
            $result[] = 'Die folgenden Mitarbeiter werden im Monat '
                . $this->formattedMonth
                . ' erwartet, aber nicht gefunden: ' . join('; ', $more_expected);
        }
        // Check that not more than the expected people have been found.
        $more_found = array_diff($this->parsedNames, $expected_people);
        if ($more_found) {
            $result[] = 'Die folgenden Mitarbeiter werden im Monat '
                . $this->formattedMonth
                . ' nicht erwartet, aber gefunden: ' . join('; ', $more_found);
        }

        return $result;
    }

    public function validateShifts()
    {
        $result = [];
        // Calculate length of episode.
        $plan_lines = explode("\n", $this->rawShifts);
        // Get first line of plan data.
        $first_line = $plan_lines[0];
        // Remove line endings, but not tabs.
        $first_line = trim($first_line, "\n\r");
        // Count the days in the line
        $submitted_days = count(explode("\t", $first_line));
        // The submitted days must be exactly one month.
        // so check that the next day is the first of a month.
        if ($submitted_days > 31) {
            $result[] = $this->formattedMonth . ': Es wurden mehr als 31 Tage in den Schichten gefunden.';
        }
        $date = date_create($this->formattedMonth . '-01');
        date_add($date, date_interval_create_from_date_string($submitted_days . ' days'));
        $end_day = date_format($date, 'd');
        if ($end_day != '01') {
            $result[] = $this->formattedMonth . ': Die Anzahl der Tage in den Schichten stimmt nicht mit der Anzahl der Tage des Monats überein.';
        }
        // Do not error out if there's one line break appended.
        if (end($plan_lines) == '') {
            array_pop($plan_lines);
        }
        // Avoid a division by zero
        if (count($this->parsedNames) == 0) {
            return $result;
        }
        // Ensure that the number of lines in plan is a multiple of people's lines.
        if ((count($plan_lines) % count($this->parsedNames)) != 0) {
            $result[] = $this->formattedMonth . ': Es wurden mehr Zeilen in den Schichten gefunden als Mitarbeiter vorhanden sind.';
        }
        $lines_per_person = count($plan_lines) / count($this->parsedNames);
        if ($lines_per_person != 1 and $lines_per_person != 3) {
            $result[] = $this->formattedMonth . ': Die Anzahl der Zeilen in den Schichten muss entweder eine oder drei pro Mitarbeiter sein.';
        }
        // Try parsing all shifts to detect unknown shifts.
        foreach ($this->parsedNames as $id => $name) {
            $shifts = $this->calculateShifts($this->parsedShifts[$id]);
            if (! is_array($shifts)) {
                // Add the error message from calculateShifts().
                $result[] = $this->formattedMonth . ': ' . $shifts;
            }
        }

        return $result;
    }
}
