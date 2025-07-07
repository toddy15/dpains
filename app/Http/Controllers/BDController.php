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
        $helper = new Helper;

        // Ensure a valid year
        if ($year < $helper->firstYear or $year > $helper->getPlannedYear()) {
            $year = $helper->getPlannedYear();
        }

        // This combines all employee data for the table (used for sorting).
        $employee_infos = [];

        // This holds the value and markup for a table cell.
        $combined_bds_first_half = [];
        $combined_bds_second_half = [];

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
                if (! isset($combined_bds_first_half[$episode->employee_id])) {
                    $combined_bds_first_half[$episode->employee_id] = array_fill(1, 6, [
                        'value' => '–',
                        'markup' => '',
                    ]);
                }
                if (! isset($combined_bds_second_half[$episode->employee_id])) {
                    $combined_bds_second_half[$episode->employee_id] = array_fill(1, 6, [
                        'value' => '–',
                        'markup' => '',
                    ]);
                }
                if (! isset($vk_in_month[$episode->employee_id])) {
                    $vk_in_month[$episode->employee_id] = array_fill(1, 12, 0);
                }
                if (! isset($bds_in_month[$episode->employee_id])) {
                    $bds_in_month[$episode->employee_id] = array_fill(1, 12, 0);
                }

                // Get VK for this employee in the current month
                $vk_in_month[$episode->employee_id][$month] = $episode->vk;

                // Get count of BDs in the current month
                $shift = $shifts->firstWhere('employee_id', $episode->employee_id);
                $bds = $shift?->bds;
                settype($bds, 'float');
                $bds_in_month[$episode->employee_id][$month] = $bds;

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
                if ($month <= 6) {
                    $combined_bds_first_half[$episode->employee_id][$month] = [
                        'value' => sprintf('%.1f', $bds),
                        'markup' => $markup,
                    ];
                } else {
                    $combined_bds_second_half[$episode->employee_id][$month - 6] = [
                        'value' => sprintf('%.1f', $bds),
                        'markup' => $markup,
                    ];
                }

                // Always use the last available information.
                $employee_infos[$episode->employee_id] = [
                    'id' => $episode->employee_id,
                    'name' => $episode->name,
                    'staffgroup_weight' => $episode->weight,
                ];
            }
        }

        // Calculate the sum of BDs per half-year
        $sum_of_bds_per_halfyear = [];
        foreach ($bds_in_month as $employee_id => $data) {
            $sum_of_bds_per_halfyear[$employee_id][0] = array_sum(array_slice($data, 0, 6));
            $sum_of_bds_per_halfyear[$employee_id][1] = array_sum(array_slice($data, 6, 6));
        }

        // Calculate the maximum allowed BDs per half-year, based on VK
        $combined_sums = [];
        foreach ($vk_in_month as $employee_id => $data) {
            // First half
            $sum_vk = array_sum(array_slice($data, 0, 6));
            $max_bds_per_halfyear[$employee_id][0] = round($sum_vk * 4, 0);
            $sum_of_months_with_vk_1 = array_sum(array_filter(array_slice($data, 0, 6), fn ($value) => $value == 1));
            if ($sum_of_months_with_vk_1 >= 3) {
                $max_bds_per_halfyear[$employee_id][0] += 1;
            }
            if ($sum_of_months_with_vk_1 == 6) {
                $max_bds_per_halfyear[$employee_id][0] += 1;
            }

            // Second half
            $sum_vk = array_sum(array_slice($data, 6, 6));
            $max_bds_per_halfyear[$employee_id][1] = round($sum_vk * 4, 0);
            $sum_of_months_with_vk_1 = array_sum(array_filter(array_slice($data, 6, 6), fn ($value) => $value == 1));
            if ($sum_of_months_with_vk_1 >= 3) {
                $max_bds_per_halfyear[$employee_id][1] += 1;
            }
            if ($sum_of_months_with_vk_1 == 6) {
                $max_bds_per_halfyear[$employee_id][1] += 1;
            }

            // Show an error if more than the maximum number
            // of BDs per half-year has been planned.
            // If the maximum number has been reached, show a warning.
            for ($halfyear = 0; $halfyear <= 1; $halfyear++) {
                $bds = $sum_of_bds_per_halfyear[$employee_id][$halfyear];
                $max_bds = $max_bds_per_halfyear[$employee_id][$halfyear];

                $markup = '';
                if ($bds > $max_bds) {
                    $markup = 'danger';
                } elseif ($bds == $max_bds and $bds != 0) {
                    $markup = 'warning';
                }

                // Create the data for a table cell with markup
                $combined_sums[$employee_id][$halfyear] = [
                    'value' => sprintf('%.1f/%d', $bds, $max_bds),
                    'markup' => $markup,
                ];
            }
        }

        // Sort by staffgroup, then by name
        $staffgroup_weight = array_column($employee_infos, 'staffgroup_weight');
        $name = array_column($employee_infos, 'name');
        array_multisort($staffgroup_weight, $name, SORT_STRING | SORT_FLAG_CASE, $employee_infos);

        $previous_year_url = $helper->getPreviousYearUrl('report/bd/', $year);
        $next_year_url = $helper->getNextYearUrl('report/bd/', $year);
        if ($year == $helper->getPlannedYear()) {
            $next_year_url = '';
        }

        return view('reports.show_bds',
            [
                'year' => $year,
                'previous_year_url' => $previous_year_url,
                'next_year_url' => $next_year_url,
                'employee_infos' => $employee_infos,
                'combined_bds_first_half' => $combined_bds_first_half,
                'combined_bds_second_half' => $combined_bds_second_half,
                'combined_sums' => $combined_sums,
            ]
        );
    }
}
