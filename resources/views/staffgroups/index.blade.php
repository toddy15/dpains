@extends('layouts.app')

@section('content')
    <h1>Gruppen</h1>
    <p>
        <x-link-button class="btn btn-primary" href="{{ route('staffgroups.create') }}">
            Neue Gruppe erstellen
        </x-link-button>
    </p>
    <table class="table table-striped">
        <thead>
            <th>Name</th>
            <th>Reihenfolge</th>
            <th>Aktion</th>
        </thead>
        <tbody>
            @foreach ($staffgroups as $staffgroup)
                <tr>
                    <td>{{ $staffgroup->staffgroup }}</td>
                    <td>{{ $staffgroup->weight }}</td>
                    <td>
                        <x-link-button class="btn btn-primary" href="{{ route('staffgroups.edit', $staffgroup) }}">
                            Bearbeiten
                        </x-link-button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
