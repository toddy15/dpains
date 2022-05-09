@extends('layouts.app')

@section('content')
    <h1>Aktuelle Mitarbeiter</h1>
    <a class="btn btn-primary" href="{{ action('App\Http\Controllers\EpisodeController@create') }}">Neuen Mitarbeiter
        anlegen</a>
    <table class="table table-striped">
        <thead>
            <th>Name</th>
            <th>E-Mail</th>
            <th>BU-Beginn</th>
            <th>Aktion</th>
        </thead>
        <tbody>
            @foreach ($current as $employee)
                @if ($employee->warning)
                    <tr class="table-danger">
                    @else
                    <tr>
                @endif
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->bu_start }}</td>
                <td>
                    <a class="btn btn-primary"
                        href="{{ action('App\Http\Controllers\EmployeeController@edit', $employee->id) }}">Bearbeiten</a>
                    <a class="btn btn-primary"
                        href="{{ action('App\Http\Controllers\EmployeeController@showEpisodes', $employee->id) }}">Einträge</a>
                </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h1>Zukünftige Mitarbeiter</h1>
    <table class="table table-striped">
        <thead>
            <th>Name</th>
            <th>E-Mail</th>
            <th>Aktion</th>
        </thead>
        <tbody>
            @foreach ($future as $employee)
                <tr>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>
                        <a class="btn btn-primary"
                            href="{{ action('App\Http\Controllers\EmployeeController@edit', $employee->id) }}">Bearbeiten</a>
                        <a class="btn btn-primary"
                            href="{{ action('App\Http\Controllers\EmployeeController@showEpisodes', $employee->id) }}">Einträge</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a class="btn btn-primary" href="{{ route('past.index') }}">
        Frühere Mitarbeiter anzeigen
    </a>
@endsection
