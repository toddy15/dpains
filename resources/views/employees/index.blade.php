@extends('layouts.app')

@section('content')
    <h1>Mitarbeitende</h1>
    <a class="btn btn-primary" href="{{ action([\App\Http\Controllers\EpisodeController::class, 'create']) }}">Neu anlegen</a>
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
                        href="{{ action([\App\Http\Controllers\EmployeeController::class, 'edit'], $employee->id) }}">Bearbeiten</a>
                    <a class="btn btn-primary"
                        href="{{ route('employees.episodes.index', ['employee' => $employee->id]) }}">Einträge</a>
                </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h1>Zukünftige Mitarbeitende</h1>
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
                            href="{{ action([\App\Http\Controllers\EmployeeController::class, 'edit'], $employee->id) }}">Bearbeiten</a>
                        <a class="btn btn-primary"
                            href="{{ route('employees.episodes.index', ['employee' => $employee]) }}">Einträge</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a class="btn btn-primary" href="{{ route('past.index') }}">
        Frühere Mitarbeitende anzeigen
    </a>
@endsection
