@extends('layouts.app')

@section('content')
    <h1>Sollzahlen der Schichten</h1>
    <p>
        <a class="btn btn-primary" href="{{ action('App\Http\Controllers\DueShiftController@create') }}">Neue Sollzahlen erstellen</a>
    </p>
    <table class="table table-striped">
        <thead>
            <th>Jahr</th>
            <th>Mitarbeitergruppe</th>
            <th>Nachtdienste</th>
            <th>NEF-Dienste</th>
            <th>Aktion</th>
        </thead>
        <tbody>
            @foreach($due_shifts as $due_shift)
                <tr>
                    <td>{{ $due_shift->year }}</td>
                    <td>
                        @if ($due_shift->staffgroup['staffgroup'] == 'FA')
                            FA und WB mit Nachtdienst
                        @else
                            {{ $due_shift->staffgroup['staffgroup'] }}
                        @endif
                    </td>
                    <td>{{ $due_shift->nights }}</td>
                    <td>{{ $due_shift->nefs }}</td>
                    <td><a class="btn btn-primary" href="{{ action('App\Http\Controllers\DueShiftController@edit', $due_shift->id) }}">Bearbeiten</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
