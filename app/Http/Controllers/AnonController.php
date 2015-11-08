<?php

namespace App\Http\Controllers;

use App\Employee;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class AnonController extends Controller
{

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
        $url = action('AnonController@showEpisodes', $employee->hash);
        Mail::queue(['text' => 'emails.new_hash'], compact('url'), function ($m) use ($employee) {
            $m->from('webmaster@dienstplan-an.de', 'Webmaster');
            $m->to($employee->email);
            $m->subject('Neuer Zugriffscode für www.dienstplan-an.de');
        });
        $request->session()->flash('info', "Der neue Zugriffscode wurde an $email gesendet.");
        return redirect(url('/'));
    }
}
