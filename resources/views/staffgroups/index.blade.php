@extends('layouts.app')

@section('content')
    <h1>Mitarbeitergruppen</h1>
    <p>
        <a class="btn btn-primary" href="{{ action('App\Http\Controllers\StaffgroupController@create') }}">Neue Mitarbeitergruppe erstellen</a>
    </p>
    <table class="table table-striped">
        <thead>
            <th>Mitarbeitergruppe</th>
            <th>Reihenfolge</th>
            <th>Aktion</th>
        </thead>
        <tbody>
            @foreach($staffgroups as $staffgroup)
                <tr>
                    <td>{{ $staffgroup->staffgroup }}</td>
                    <td>{{ $staffgroup->weight }}</td>
                    <td><a class="btn btn-primary" href="{{ action('App\Http\Controllers\StaffgroupController@edit', $staffgroup->id) }}">Bearbeiten</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
