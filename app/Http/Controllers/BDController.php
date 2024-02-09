<?php

namespace App\Http\Controllers;

use App\Services\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BDController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $year = 0): View
    {
        $helper = new Helper();

        // Ensure a valid year
        if ($year < $helper->firstYear or $year > $helper->getPlannedYear()) {
            $year = $helper->getPlannedYear();
        }

        // Get all analyzed months for the given year
        $combined_bds = [];
        $quarterly_extra_bd = [];
        $employee_info = [];
        for ($month = 1; $month <= 12; $month++) {
            $formattedMonth = $helper->validateAndFormatDate($year, $month);
            $episodes = $helper->getPeopleForMonth($formattedMonth);

            // Get all analyzed shifts for the current month
            $shifts = DB::table('analyzed_months')
                ->where('month', $formattedMonth)
                ->get();

            foreach ($episodes as $episode) {
                // Fill up array if it didn't exist
                if (! isset($combined_bds[$episode->employee_id])) {
                    $combined_bds[$episode->employee_id] = array_fill(1, 12, [
                        'stats' => '–',
                        'markup' => '',
                    ]);
                }
                if (! isset($quarterly_extra_bd[$episode->employee_id])) {
                    $quarterly_extra_bd[$episode->employee_id] = array_fill(0, 4, false);
                }

                $shift = $shifts->firstWhere('employee_id', $episode->employee_id);
                if ($shift === null) {
                    $bds = 0;
                } else {
                    $bds = $shift->bds;
                }
                $max_bds = round(4 * $episode->vk, 0);

                $quarter = floor(($month - 1) / 3);

                // Show a warning if the extra BD per quarter has been used.
                // Show a stronger warning if the extra BD has already been used or
                // if the number of BDs in this month is greater than max_bds + 1.
                $markup = '';
                if ($bds > ($max_bds + 1)) {
                    $markup = 'danger';
                } elseif ($bds > $max_bds and $quarterly_extra_bd[$episode->employee_id][$quarter]) {
                    $markup = 'danger';
                } elseif ($bds > $max_bds) {
                    // Keep track of one extra BD per quarter.
                    $quarterly_extra_bd[$episode->employee_id][$quarter] = true;
                    $markup = 'warning';
                }

                // Combine actual and max BDs into one table cell
                $combined_bds[$episode->employee_id][$month] = [
                    'stats' => sprintf('%.1f/%d', $bds, $max_bds),
                    'markup' => $markup,
                ];

                // Always use the last available information.
                $employee_info[$episode->employee_id] = [
                    'id' => $episode->employee_id,
                    'name' => $episode->name,
                    'staffgroup_weight' => $episode->weight,
                ];
            }
        }

        // Sort by staffgroup, then by name
        $staffgroup_weight = array_column($employee_info, 'staffgroup_weight');
        $name = array_column($employee_info, 'name');
        array_multisort($staffgroup_weight, $name, SORT_STRING | SORT_FLAG_CASE, $employee_info);

        $previous_year_url = $helper->getPreviousYearUrl('report/bd/', $year);
        $next_year_url = $helper->getNextYearUrl('report/bd/', $year);

        return view('reports.show_bds',
            [
                'year' => $year,
                'previous_year_url' => $previous_year_url,
                'next_year_url' => $next_year_url,
                'employee_info' => $employee_info,
                'combined_bds' => $combined_bds,
            ]
        );
    }
}
