@extends('layouts.app')

@section('content')
    <h1>Sollzahlen der Schichten</h1>
    <p>
        <x-link-button class="btn-primary" href="{{ route('due_shifts.create') }}">Neue Sollzahlen
            erstellen
        </x-link-button>
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
                <td>
                    <x-link-button class="btn-primary" href="{{ route('due_shifts.edit', $due_shift) }}">Bearbeiten
                    </x-link-button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
