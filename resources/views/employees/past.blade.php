@extends('app')

@section('content')
    <h1>Frühere Mitarbeiter</h1>
    <table class="table table-striped">
        <thead>
        <th>Name</th>
        <th>E-Mail</th>
        <th>Aktion</th>
        </thead>
        <tbody>
        @foreach($past as $employee)
            @if ($employee->warning)
                <tr class="danger">
            @else
                <tr>
            @endif
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
@endsection
