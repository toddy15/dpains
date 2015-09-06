@extends('app')

@section('content')
    <h1>Einträge für XX</h1>
    <p>
        <a class="btn btn-primary" href="{{ action('EpisodeController@create', ['number' => $number]) }}">Neuen Eintrag erstellen</a>
    </p>
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
                <td>{{ $episode->job_id }}</td>
                <td>{{ $episode->vk }}</td>
                <td>{{ $episode->factor_night }}</td>
                <td>{{ $episode->factor_nef }}</td>
                <td>{{ $episode->comment['comment'] }}</td>
                <td><a class="btn btn-primary" href="{{ action('EpisodeController@edit', $episode->id) }}">Bearbeiten</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
