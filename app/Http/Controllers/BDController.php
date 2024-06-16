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

        // This combines all employee data for the table (used for sorting).
        $employee_infos = [];

        // This holds the value and markup for a table cell.
        $combined_bds = [];

        // This holds the VK of each month for an employee.
        $vk_in_month = [];

        // This holds the count of BDs in each month for an employee.
        $bds_in_month = [];

        // Get all analyzed months for the given year
        for ($month = 1; $month <= 12; $month++) {
            $formattedMonth = $helper->validateAndFormatDate($year, $month);
            $episodes = $helper->getPeopleForMonth($formattedMonth);

            // Get all analyzed shifts for the current month
            $shifts = DB::table('analyzed_months')
                ->where('month', $formattedMonth)
                ->get();

            foreach ($episodes as $episode) {
                // Initialize up arrays if they didn't exist
                if (! isset($vk_in_month[$episode->employee_id])) {
                    $vk_in_month[$episode->employee_id] = array_fill(1, 12, 0);
                }
                if (! isset($bds_in_month[$episode->employee_id])) {
                    $bds_in_month[$episode->employee_id] = array_fill(1, 12, 0);
                }

                // Get VK for this employee in the current month
                $vk_in_month[$episode->employee_id][$month] = $episode->vk;

                // Get count of BDs in the current month
                $bds = 0;
                $shift = $shifts->firstWhere('employee_id', $episode->employee_id);
                if ($shift !== null) {
                    $bds = $shift->bds;
                }
                $bds_in_month[$episode->employee_id] = $bds;

                // Calculate the absolute maximum of BDs
                $max_bds = round(7 * $episode->vk, 0);

                // Show an error if more than the maximum number
                // of BDs per month has been planned.
                // If the maximum number has been reached, show a warning.
                $markup = '';
                if ($bds > $max_bds) {
                    $markup = 'danger';
                } elseif ($bds == $max_bds and $bds != 0) {
                    $markup = 'warning';
                }

                // Combine actual BDs and markup into one table cell
                $combined_bds[$episode->employee_id][$month] = [
                    'value' => sprintf('%.1f/%.3f', $bds, $episode->vk),
                    'markup' => $markup,
                ];

                // Always use the last available information.
                $employee_infos[$episode->employee_id] = [
                    'id' => $episode->employee_id,
                    'name' => $episode->name,
                    'staffgroup_weight' => $episode->weight,
                ];
            }
        }

        // Sort by staffgroup, then by name
        $staffgroup_weight = array_column($employee_infos, 'staffgroup_weight');
        $name = array_column($employee_infos, 'name');
        array_multisort($staffgroup_weight, $name, SORT_STRING | SORT_FLAG_CASE, $employee_infos);

        $previous_year_url = $helper->getPreviousYearUrl('report/bd/', $year);
        $next_year_url = $helper->getNextYearUrl('report/bd/', $year);

        return view('reports.show_bds',
            [
                'year' => $year,
                'previous_year_url' => $previous_year_url,
                'next_year_url' => $next_year_url,
                'employee_infos' => $employee_infos,
                'combined_bds' => $combined_bds,
            ]
        );
    }
}
