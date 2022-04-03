@extends('layouts.app')

@section('content')
    <h1>{{ $readable_month }}</h1>
    <nav aria-label="Navigation des Monats">
        <ul class="pagination">
            <li class="page-item {{ empty($previous_month_url) ? 'disabled' : '' }}"><a class="page-link" href="{{ $previous_month_url }}"><span aria-hidden="true">&larr;</span>
                    Vorheriger Monat</a></li>
            <li class="page-item"><a class="page-link" href="{{ $next_month_url }}">Nächster Monat <span aria-hidden="true">&rarr;</span></a></li>
        </ul>
    </nav>
    @if (count($episode_changes))
        <h2>Änderungen</h2>
        <table class="table table-striped">
            <thead>
            <th>Name</th>
            <th>Beginn</th>
            <th>Mitarbeitergruppe</th>
            <th>VK</th>
            <th>Faktor Nachtdienst</th>
            <th>Faktor NEF</th>
            <th>Bemerkung</th>
            <th>Aktion</th>
            </thead>
            <tbody>
            @foreach($episode_changes as $episode)
                <tr>
                    <td>{{ $episode->name }}</td>
                    <td>{{ $episode->start_date }}</td>
                    <td>{{ $episode->staffgroup }}</td>
                    <td>{{ $episode->vk }}</td>
                    <td>{{ $episode->factor_night }}</td>
                    <td>{{ $episode->factor_nef }}</td>
                    <td>{{ $episode->comment }}</td>
                    <td><a class="btn btn-primary" href="{{ action('App\Http\Controllers\EmployeeController@showEpisodes', $episode->employee_id) }}">Bearbeiten</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endunless
    <h2>Mitarbeiter</h2>
    <table class="table table-striped">
        <thead>
        <th>Name</th>
        <th>Beginn</th>
        <th>Mitarbeitergruppe</th>
        <th>VK</th>
        <th>Faktor Nachtdienst</th>
        <th>Faktor NEF</th>
        <th>Bemerkung</th>
        <th>Aktion</th>
        </thead>
        <tbody>
        @foreach($episodes as $episode)
            <tr>
                <td>{{ $episode->name }}</td>
                <td>{{ $episode->start_date }}</td>
                <td>{{ $episode->staffgroup }}</td>
                <td>{{ $episode->vk }}</td>
                <td>{{ $episode->factor_night }}</td>
                <td>{{ $episode->factor_nef }}</td>
                <td>{{ $episode->comment }}</td>
                <td><a class="btn btn-primary" href="{{ action('App\Http\Controllers\EmployeeController@showEpisodes', $episode->employee_id) }}">Bearbeiten</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
