<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestNewHashPerMailAnonRequest;
use App\Mail\NewHash;
use App\Models\Employee;
use App\Models\Rawplan;
use App\Services\Helper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AnonController extends Controller
{
    /**
     * Show the homepage
     */
    public function homepage(Request $request, string $hash = ''): View
    {
        if ($hash !== '') {
            $employee = Employee::where('hash', $hash)->first();
            // Feedback if there is no such hash
            if (! $employee) {
                $request
                    ->session()
                    ->flash('warning', 'Dieser Zugriffcode ist nicht gültig.');
                $hash = '';
            } else {
                // Refresh last access
                $employee->touch();
            }
        }

        return view('homepage', ['hash' => $hash]);
    }

    /**
     * Logout current user by disabling the hash
     */
    public function logout(Request $request, string $hash): RedirectResponse
    {
        $employee = Employee::where('hash', $hash)->first();
        // Feedback if there is no such hash
        if (! $employee) {
            $request
                ->session()
                ->flash('warning', 'Dieser Zugriffcode ist nicht gültig.');

            return redirect(url('/'));
        }

        // Remove the currently valid hash
        $employee->hash = Str::random();
        $employee->save();
        $request->session()->flash('info', 'Du wurdest abgemeldet.');

        return to_route('homepage');
    }

    /**
     * Show all episodes for an employee, using anonymous access.
     *
     * The hash is mapped to the employees id.
     */
    public function showEpisodes(Request $request, string $hash): View|RedirectResponse
    {
        $employee = Employee::where('hash', $hash)->first();
        // Feedback if there is no such hash
        if (! $employee) {
            $request
                ->session()
                ->flash('warning', 'Dieser Zugriffcode ist nicht gültig.');

            return redirect(url('/'));
        }

        // Refresh last access
        $employee->touch();
        $episodes = $employee
            ->episodes()
            ->oldest('start_date')
            ->get();
        $latest_name = $employee->name;

        return view('anon.show_episodes',
            [
                'hash' => $hash,
                'episodes' => $episodes,
                'latest_name' => $latest_name,
            ],
        );
    }

    /**
     * Show the current year overview.
     *
     * The hash is mapped to the employees id.
     */
    public function showCurrentYear(Helper $helper, Request $request, string $hash): View|RedirectResponse
    {
        return $this->showYear($helper, $request, $helper->getPlannedYear(), $hash);
    }

    /**
     * Show the year overview.
     *
     * The hash is mapped to the employees id.
     */
    public function showYear(Helper $helper, Request $request, int $year, string $hash): View|RedirectResponse
    {
        $employee = Employee::where('hash', $hash)->first();
        // Feedback if there is no such hash
        if (! $employee) {
            $request
                ->session()
                ->flash('warning', 'Dieser Zugriffcode ist nicht gültig.');

            return redirect(url('/'));
        }

        // Refresh last access
        $employee->touch();
        // Determine which month has been planned
        $planned_month = $helper->getPlannedMonthForAnonAccess($year);
        if (! $planned_month) {
            // There is no data at all, so abort.
            abort(404);
        }

        // Determine which month is in the past and therefore
        // represents the actually worked shifts.
        $worked_month = $helper->getWorkedMonth($year);
        // Set up readable month names
        Carbon::setLocale('de');
        $readable_planned_month = Carbon::parse($planned_month)
            ->isoFormat('MMMM YYYY');
        $readable_worked_month = '';
        if ($worked_month !== null) {
            $readable_worked_month = Carbon::parse($worked_month)
                ->isoFormat('MMMM YYYY');
        }

        // Get the date and time of latest change
        $latest_change = Rawplan::where('anon_report', 1)
            ->latest('updated_at')
            ->value('updated_at');
        $latest_change = Carbon::parse($latest_change)
            ->timezone('Europe/Berlin')
            ->isoFormat('Do MMMM YYYY, HH:mm');
        // Generate the next and previous year urls
        $previous_year_url = $helper->getPreviousYearUrl('anon/', $year);
        if ($previous_year_url !== '') {
            $previous_year_url .= '/'.$hash;
        }

        $next_year_url = $helper->getNextYearUrl('anon/', $year).'/'.$hash;
        $tables = $helper->getTablesForYear(
            $request,
            $year,
            $worked_month,
            $employee->id,
        );

        return view('anon.show_year',
            [
                'hash' => $hash,
                'year' => $year,
                'previous_year_url' => $previous_year_url,
                'next_year_url' => $next_year_url,
                'latest_change' => $latest_change,
                'readable_planned_month' => $readable_planned_month,
                'readable_worked_month' => $readable_worked_month,
                'tables' => $tables,
                'helper' => $helper,
            ]
        );
    }

    /**
     * Request a new hash via mail for accessing the stats.
     *
     * @throws ValidationException
     */
    public function requestNewHashPerMail(RequestNewHashPerMailAnonRequest $request): RedirectResponse
    {
        $email = trim((string) $request->input('email'));
        // Append the domain, if necessary
        if (! Str::contains($email, '@')) {
            $email .= '@asklepios.com';
        }

        $employee = Employee::where('email', $email)->first();
        // Feedback if there is no such mail
        if (! $employee) {
            $request
                ->session()
                ->flash('warning', "Die E-Mail {$email} wurde nicht gefunden.");

            return redirect(url('/'));
        }

        // Generate a new hash with some pseudo random bits
        $employee->hash = Str::random();
        $employee->save();
        // Send the mail
        $url = action(
            [AnonController::class, 'showYear'],
            [Carbon::now()->yearIso, $employee->hash],
        );

        Mail::to($employee->email)->queue(new NewHash($url));

        $request
            ->session()
            ->flash('info', "Der neue Zugriffscode wurde an {$email} gesendet.");

        return to_route('homepage');
    }
}
