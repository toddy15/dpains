<?php

namespace App\Dpains;

class Planparser
{
    public $names = [];
    public $shifts = [];

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
}
