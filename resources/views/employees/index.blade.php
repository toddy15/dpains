@extends('app')

@section('content')
    <h1>Aktuelle Mitarbeiter</h1>
    <a class="btn btn-primary" href="{{ action('EpisodeController@create') }}">Neuen Mitarbeiter anlegen</a>
    <table class="table table-striped">
        <thead>
        <th>Name</th>
        <th>E-Mail</th>
        <th>BU-Beginn</th>
        <th>Aktion</th>
        </thead>
        <tbody>
        @foreach($current as $employee)
            <tr>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->bu_start }}</td>
                <td>
                    <a class="btn btn-primary"
                       href="{{ action('EmployeeController@edit', $employee->id) }}">Bearbeiten</a>
                    <a class="btn btn-primary"
                       href="{{ action('EmployeeController@showEpisodes', $employee->id) }}">Einträge</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h1>Frühere und zukünftige Mitarbeiter</h1>
    <table class="table table-striped">
        <thead>
        <th>Name</th>
        <th>E-Mail</th>
        <th>Aktion</th>
        </thead>
        <tbody>
        @foreach($past_and_future as $employee)
            <tr>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->email }}</td>
                <td>
                    <a class="btn btn-primary"
                       href="{{ action('EmployeeController@edit', $employee->id) }}">Bearbeiten</a>
                    <a class="btn btn-primary"
                       href="{{ action('EmployeeController@showEpisodes', $employee->id) }}">Einträge</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
