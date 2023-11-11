<?php

namespace App\Http\Controllers;

use App\Services\Helper;
use Illuminate\Http\Request;
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
        $max_bd_per_month = [];
        $employee_info = [];
        for ($month = 1; $month <= 12; $month++) {
            $episodes = $helper->getPeopleForMonth(
                $helper->validateAndFormatDate($year, $month)
            );

            foreach ($episodes as $episode) {
                // Fill up array if it didn't exist
                if (! isset($max_bd_per_month[$episode->employee_id])) {
                    $max_bd_per_month[$episode->employee_id] = array_fill(1, 12, 0);
                }
                $max_bd_per_month[$episode->employee_id][$month] = round(4 * $episode->vk, 0);

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
                'max_bd_per_month' => $max_bd_per_month,
            ]
        );
    }
}
