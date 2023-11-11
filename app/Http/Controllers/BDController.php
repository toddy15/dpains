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
        if ($year < $helper->firstYear or $year > (int) $helper->getPlannedYear()) {
            $year = (int) $helper->getPlannedYear();
        }

        // Get all analyzed months for the given year
        $combined_bds = [];
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
                        'warning' => false,
                    ]);
                }

                $shift = $shifts->firstWhere('employee_id', $episode->employee_id);
                if ($shift === null) {
                    $bds = 0;
                } else {
                    $bds = $shift->bds;
                }
                $max_bds = round(4 * $episode->vk, 0);

                // Combine actual and max BDs into one table cell
                $combined_bds[$episode->employee_id][$month] = [
                    'stats' => $bds.'/'.$max_bds,
                    'warning' => $bds > $max_bds,
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
