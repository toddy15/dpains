<?php

namespace App\Http\Controllers;

use App\Dpains\Helper;
use App\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class AnonController extends Controller
{
    /**
     * Show the homepage
     *
     * @param string $hash
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function homepage($hash = '')
    {
        return view('homepage', compact('hash'));
    }

    /**
     * Logout current user by disabling the hash
     *
     * @param string $hash
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout($hash)
    {
        // Remove the currently valid hash
        $employee = Employee::where('hash', $hash)->first();
        if ($employee) {
            $employee->hash = str_random();
            $employee->save();
        }
        return redirect(url('/'));
    }

    /**
     * Show all episodes for an employee, using anonymous access.
     *
     * The hash is mapped to the employees id.
     *
     * @param string $hash
     * @return \Illuminate\View\View
     */
    public function showEpisodes(Request $request, $hash)
    {
        $employee = Employee::where('hash', $hash)->first();
        // Feedback if there is no such hash
        if (!$employee) {
            $request->session()->flash('warning', 'Dieser Zugriffcode ist nicht gültig.');
            return redirect(url('/'));
        }
        $episodes = $employee->episodes()->orderBy('start_date')->get();
        $latest_name = $employee->name;
        return view('anon.show_episodes', compact('hash', 'episodes', 'latest_name'));
    }

    /**
     * Show the year overview.
     *
     * The hash is mapped to the employees id.
     *
     * @param string $hash
     * @return \Illuminate\View\View
     */
    public function showYear(Request $request, $year, $hash)
    {
        $employee = Employee::where('hash', $hash)->first();
        // Feedback if there is no such hash
        if (!$employee) {
            $request->session()->flash('warning', 'Dieser Zugriffcode ist nicht gültig.');
            return redirect(url('/'));
        }
        // Determine which month has been planned
        $planned_month = Helper::getPlannedMonth($year);
        if (!$planned_month) {
            // There is no data at all, so abort.
            abort(404);
        }
        // Determine which month is in the past and therefore
        // represents the actually worked shifts.
        $worked_month = Helper::getWorkedMonth($year);
        // Set up readable month names
        $readable_planned_month = Carbon::parse($planned_month)->formatLocalized('%B %Y');
        $readable_worked_month = '';
        if (!empty($worked_month)) {
            $readable_worked_month = Carbon::parse($worked_month)->formatLocalized('%B %Y');
        }
        $tables = Helper::getTablesForYear($request, $year, $worked_month, $employee->id);
        return view('anon.show_year', compact('hash', 'year',
            'readable_planned_month', 'readable_worked_month', 'tables'));
    }

    /**
     * Request a new hash via mail for accessing the stats.
     */
    public function requestNewHashPerMail(Request $request) {
        $this->validate($request, [
            'email' => 'required|email'
        ]);
        $email = $request->get('email');
        $employee = Employee::where('email', $email)->first();
        // Feedback if there is no such mail
        if (!$employee) {
            $request->session()->flash('warning', "Die E-Mail $email wurde nicht gefunden.");
            return redirect(url('/'));
        }
        // Generate a new hash with some pseudo random bits
        $employee->hash = str_random();
        $employee->save();
        // Send the mail
        // @TODO: Do not hardcode
        $url = action('AnonController@showYear', 2016, $employee->hash);
        Mail::queue(['text' => 'emails.new_hash'], compact('url'), function ($m) use ($employee) {
            $m->to($employee->email);
            $m->subject('Neuer Zugriffscode für www.dienstplan-an.de');
        });
        $request->session()->flash('info', "Der neue Zugriffscode wurde an $email gesendet.");
        return redirect(url('/'));
    }
}
