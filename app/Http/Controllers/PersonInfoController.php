<?php

namespace App\Http\Controllers;

use App\Episode;
use App\PersonInfo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class PersonInfoController extends Controller
{
    /**
     * Show all episodes for the given person number.
     *
     * @param int $number
     * @return \Illuminate\View\View
     */
    public function show($number)
    {
        $episodes = Episode::where('number', '=', $number)
            ->orderBy('start_date')->get();
        if (!count($episodes)) {
            abort(404);
        }
        // Get the name of the latest episode.
        $latest_name = $episodes->last()->name;
        return view('people.show', compact('episodes', 'number', 'latest_name'));
    }

    /**
     * Show information of all people
     */
    public function index() {
        // Set up different result array for table grouping in the view
        $email_complete = [];
        $email_missing = [];
        // Find all person numbers
        $numbers = PersonInfo::numbers();
        foreach ($numbers as $number => $name) {
            $person_info = PersonInfo::firstOrNew(['number' => $number]);
            $person_info->name = $name;
            if ($person_info->email) {
                $email_complete[] = $person_info;            }
            else {
                $email_missing[] = $person_info;
            }
        }
        return view('people.show_emails', compact('email_complete', 'email_missing'));
    }

    /**
     * Show all episodes for a person, using anonymous access.
     *
     * The hash is mapped to the person's number.
     *
     * @param string $hash
     * @return \Illuminate\View\View
     */
    public function anonEpisodes(Request $request, $hash)
    {
        $person_info = PersonInfo::where('hash', $hash)->first();
        // Feedback if there is no such hash
        if (!$person_info) {
            $request->session()->flash('warning', 'Dieser Zugriffcode ist nicht gültig.');
            return redirect(url('/'));
        }
        return $this->show($person_info->number);
    }

    /**
     * Request a new hash via mail for accessing the stats.
     */
    public function requestNewHashPerMail(Request $request) {
        $email = $request->get('email');
        $person_info = PersonInfo::where('email', $email)->first();
        // Feedback if there is no such mail
        if (!$person_info) {
            $request->session()->flash('warning', "Die E-Mail $email wurde nicht gefunden.");
            return redirect(url('/'));
        }
        // Generate a new hash with some pseudo random bits
        $person_info->hash = hash('sha256', microtime() . $person_info->email);
        $person_info->save();
        // Send the mail
        $url = action('PersonInfoController@anonEpisodes', $person_info->hash);
        Mail::queue(['text' => 'emails.new_hash'], compact('url'), function ($m) use ($person_info) {
            $m->from('webmaster@dienstplan-an.de', 'Webmaster');
            $m->to($person_info->email);
            $m->subject('Neuer Zugriffscode für www.dienstplan-an.de');
        });
        $request->session()->flash('info', "Der neue Zugriffscode wurde an $email gesendet.");
        return redirect(url('/'));
    }
}
