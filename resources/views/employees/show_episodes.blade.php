@extends('layouts.app')

@section('content')
    <h1>Einträge für {{ $latest_name }}</h1>
    <p>
        <a class="btn btn-primary"
            href="{{ action('App\Http\Controllers\EpisodeController@create', ['employee_id' => $id]) }}">Neuen Eintrag
            erstellen</a>
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
            @foreach ($episodes as $episode)
                <tr>
                    <td>{{ $episode->name }}</td>
                    <td>{{ $episode->start_date }}</td>
                    <td>{{ $episode->staffgroup['staffgroup'] }}</td>
                    <td>{{ $episode->vk }}</td>
                    <td>{{ $episode->factor_night }}</td>
                    <td>{{ $episode->factor_nef }}</td>
                    <td>{{ $episode->comment['comment'] ?? '' }}</td>
                    <td>
                        {!! Form::open(['action' => ['App\Http\Controllers\EpisodeController@destroy', $episode->id], 'method' => 'delete']) !!}
                        <a class="btn btn-primary"
                            href="{{ action('App\Http\Controllers\EpisodeController@edit', $episode->id) }}">Bearbeiten</a>
                        {!! Form::submit('Löschen', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
