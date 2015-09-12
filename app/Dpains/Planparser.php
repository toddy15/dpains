<?php

namespace App\Dpains;

use Illuminate\Support\Facades\DB;

class Planparser
{
    public $month = '';
    public $names = [];
    public $shifts = [];

    /**
     * Planparser constructor.
     * @param string $month
     */
    public function __construct($month)
    {
        $this->month = $month;
    }


    public function parseNames($people)
    {
        $this->names = [];
        $person_lines = explode("\n", $people);
        foreach ($person_lines as $person_line) {
            // Remove whitespace.
            $person_line = trim($person_line);
            // Remove comma, space and end dates from names.
            $person_line = preg_replace("/\s*,?\s*[0-9.]+$/", '', $person_line);
            // Skip empty lines
            if (strlen($person_line) == 0) {
                continue;
            }
            // Finally, add the name to list.
            $this->names[] = $person_line;
        }
    }

    public function parseShifts($shifts)
    {
        $this->shifts = [];
        $plan_lines = explode("\n", $shifts);
        // Determine if there are 1 or 3 lines per person.
        $lines_per_person = count($plan_lines) / count($this->names);
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
            $this->shifts = $plan;
        } elseif ($lines_per_person == 3) {
            $this->shifts = $work;
        }
    }

    public function storeShiftsForPerson()
    {
        // Clean all previously parsed results.
        DB::table('analyzed_months')->where('month', $this->month)->delete();
        // Get people names for this episode.
        $reporter = new Reporter();
        // Get an array with the unique person's number and name
        $expected_names = $reporter->getNamesForMonth($this->month);
        $database_rows = [];
        foreach ($this->names as $id => $name) {
            $person_number = array_search($name, $expected_names);
            $shifts = $this->calculateShifts($this->shifts[$id]);
            if (!is_array($shifts)) {
                drupal_set_message(t('Plan data not saved.'), 'error');
                // Clean all previously parsed results.
                db_delete('ains_parsed_months')
                    ->condition('month', $month)
                    ->execute();
                return;
            }
            $database_rows[] = [
                'month' => $this->month,
                'number' => $person_number,
                'nights' => $shifts['nights'],
                'nefs' => $shifts['nefs'],
            ];
        }
        DB::table('analyzed_months')->insert($database_rows);
        // @TODO: FEEDBACK
    }

    public function calculateShifts($shifts)
    {
        // @TODO: Do not hardcode.
        $nights = ['0r', 'D0', 'D1', 'i30', 'i36', 'n2'];
        $nefs = ['n1', 'n2'];
        $ignored = ['1', '2', 'st', '25', '26', '27', 'D2', 'i28', 'i29', 'i33', 'i35', 'dt0', 'dt1',
            'FÜ', 'BF', 'TZ', 'MS', 'EZ', 'U', 'FBi*', 'FBe*', 'Con', 'K', 'KO', 'Kol', 'KP', 'KK',
            'KÜ', 'ZU', 'BR', 'BU', 'F.', '', 'DB', 'Ve', 'US', '--', 'BV'
        ];
        $night_counter = 0;
        $nef_counter = 0;
        foreach ($shifts as $shift) {
            $unknown_shift = TRUE;
            if (in_array($shift, $nights)) {
                $night_counter++;
                $unknown_shift = FALSE;
            }
            if (in_array($shift, $nefs)) {
                $nef_counter++;
                $unknown_shift = FALSE;
            }
            if (in_array($shift, $ignored)) {
                $unknown_shift = FALSE;
            }
            if ($unknown_shift) {
                return 'Unbekannte Dienstart: ' . $shift;
            }
        }
        return ['nights' => $night_counter, 'nefs' => $nef_counter];
    }
}
