<?php

namespace App\Http\Controllers;

use App\Dpains\Helper;
use App\Employee;
use App\Rawplan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AnonController extends Controller
{
    /**
     * Show the homepage
     *
     * @param Request $request
     * @param string $hash
     * @return View
     */
    public function homepage(Request $request, string $hash = ''): View
    {
        if (! empty($hash)) {
            $employee = Employee::where('hash', $hash)->first();
            // Feedback if there is no such hash
            if (! $employee) {
                $request->session()->flash('warning', 'Dieser Zugriffcode ist nicht gültig.');
                $hash = '';
            } else {
                // Refresh last access
                $employee->touch();
            }
        }

        return view('homepage', compact('hash'));
    }

    /**
     * Logout current user by disabling the hash
     *
     * @param Request $request
     * @param string $hash
     * @return RedirectResponse
     */
    public function logout(Request $request, string $hash): RedirectResponse
    {
        $employee = Employee::where('hash', $hash)->first();
        // Feedback if there is no such hash
        if (! $employee) {
            $request->session()->flash('warning', 'Dieser Zugriffcode ist nicht gültig.');

            return redirect(url('/'));
        }
        // Remove the currently valid hash
        $employee->hash = Str::random();
        $employee->save();
        $request->session()->flash('info', 'Du wurdest abgemeldet.');

        return redirect(url('/'));
    }

    /**
     * Show all episodes for an employee, using anonymous access.
     *
     * The hash is mapped to the employees id.
     *
     * @param Request $request
     * @param string $hash
     * @return View|RedirectResponse
     */
    public function showEpisodes(Request $request, string $hash): View|RedirectResponse
    {
        $employee = Employee::where('hash', $hash)->first();
        // Feedback if there is no such hash
        if (! $employee) {
            $request->session()->flash('warning', 'Dieser Zugriffcode ist nicht gültig.');

            return redirect(url('/'));
        }
        // Refresh last access
        $employee->touch();
        $episodes = $employee->episodes()->orderBy('start_date')->get();
        $latest_name = $employee->name;

        return view('anon.show_episodes', compact('hash', 'episodes', 'latest_name'));
    }

    /**
     * Show the year overview.
     *
     * The hash is mapped to the employees id.
     *
     * @param Request $request
     * @param $year
     * @param string $hash
     * @return View|RedirectResponse
     */
    public function showYear(Request $request, $year, string $hash): View|RedirectResponse
    {
        $employee = Employee::where('hash', $hash)->first();
        // Feedback if there is no such hash
        if (! $employee) {
            $request->session()->flash('warning', 'Dieser Zugriffcode ist nicht gültig.');

            return redirect(url('/'));
        }
        // Refresh last access
        $employee->touch();
        // Determine which month has been planned
        $planned_month = Helper::getPlannedMonthForAnonAccess($year);
        if (! $planned_month) {
            // There is no data at all, so abort.
            abort(404);
        }
        // Determine which month is in the past and therefore
        // represents the actually worked shifts.
        $worked_month = Helper::getWorkedMonth($year);
        // Set up readable month names
        $readable_planned_month = Carbon::parse($planned_month)->formatLocalized('%B %Y');
        $readable_worked_month = '';
        if (! empty($worked_month)) {
            $readable_worked_month = Carbon::parse($worked_month)->formatLocalized('%B %Y');
        }
        // Get the date and time of latest change
        $latest_change = Rawplan::where('anon_report', 1)->orderBy('updated_at', 'desc')->value('updated_at');
        $latest_change = Carbon::parse($latest_change)->formatLocalized('%e. %B %Y, %H:%M');
        // Generate the next and previous year urls
        $previous_year_url = Helper::getPreviousYearUrl('anon/', $year);
        if (! empty($previous_year_url)) {
            $previous_year_url .= '/' . $hash;
        }
        $next_year_url = Helper::getNextYearUrl('anon/', $year) . '/' . $hash;
        $tables = Helper::getTablesForYear($request, $year, $worked_month, $employee->id);

        return view('anon.show_year', compact(
            'hash',
            'year',
            'previous_year_url',
            'next_year_url',
            'latest_change',
            'readable_planned_month',
            'readable_worked_month',
            'tables'
        ));
    }

    /**
     * Request a new hash via mail for accessing the stats.
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function requestNewHashPerMail(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'email' => 'required',
        ]);
        $email = trim($request->get('email'));
        // Append the domain, if necessary
        if (! str_contains($email, '@')) {
            $email .= '@asklepios.com';
        }
        $employee = Employee::where('email', $email)->first();
        // Feedback if there is no such mail
        if (! $employee) {
            $request->session()->flash('warning', "Die E-Mail $email wurde nicht gefunden.");

            return redirect(url('/'));
        }
        // Generate a new hash with some pseudo random bits
        $employee->hash = Str::random();
        $employee->save();
        // Send the mail
        $url = action('AnonController@showYear', [date('Y'), $employee->hash]);
        Mail::queue(['text' => 'emails.new_hash'], compact('url'), function ($m) use ($employee) {
            $m->to($employee->email);
            $m->subject('Neuer Zugriffscode für www.dienstplan-an.de');
        });
        $request->session()->flash('info', "Der neue Zugriffscode wurde an $email gesendet.");

        return redirect(url('/'));
    }
}
