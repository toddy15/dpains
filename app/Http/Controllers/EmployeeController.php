<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Episode;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);
        $employee = Employee::findOrFail($id);
        $employee->update($request->all());
        $request->session()->flash('info', 'Der Mitarbeiter wurde geändert.');
        return redirect(action('EmployeeController@index'));
    }

    /**
     * Show all episodes for the given employee id.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showEpisodes($id)
    {
        $employee = Employee::findOrFail($id);
        $episodes = $employee->episodes()->orderBy('start_date')->get();
        $latest_name = $employee->name;
        return view('employees.show_episodes', compact('episodes', 'id', 'latest_name'));
    }

    /**
     * Show all episodes for an employee, using anonymous access.
     *
     * The hash is mapped to the employees id.
     *
     * @param string $hash
     * @return \Illuminate\View\View
     */
    public function showAnonEpisodes(Request $request, $hash)
    {
        $employee = Employee::where('hash', $hash)->first();
        // Feedback if there is no such hash
        if (!$employee) {
            $request->session()->flash('warning', 'Dieser Zugriffcode ist nicht gültig.');
            return redirect(url('/'));
        }
        return $this->showEpisodes($employee->id);
    }

    /**
     * Request a new hash via mail for accessing the stats.
     */
    public function requestNewHashPerMail(Request $request) {
        $email = $request->get('email');
        $employee = Employee::where('email', $email)->first();
        // Feedback if there is no such mail
        if (!$employee) {
            $request->session()->flash('warning', "Die E-Mail $email wurde nicht gefunden.");
            return redirect(url('/'));
        }
        // Generate a new hash with some pseudo random bits
        $employee->hash = hash('sha256', microtime() . $employee->email);
        $employee->save();
        // Send the mail
        $url = action('EmployeeController@showAnonEpisodes', $employee->hash);
        Mail::queue(['text' => 'emails.new_hash'], compact('url'), function ($m) use ($employee) {
            $m->from('webmaster@dienstplan-an.de', 'Webmaster');
            $m->to($employee->email);
            $m->subject('Neuer Zugriffscode für www.dienstplan-an.de');
        });
        $request->session()->flash('info', "Der neue Zugriffscode wurde an $email gesendet.");
        return redirect(url('/'));
    }
}
