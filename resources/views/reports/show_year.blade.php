@extends('app')

@section('content')
    <h1>{{ $year }}</h1>
    @foreach($tables as $staffgroup => $table)
        <h2>{{ $staffgroup }}</h2>
        <table class="table table-striped">
            <thead>
                <th>Name</th>
                <th>Gearbeitete Nachtdienste</th>
                <th>Geplante Nachtdienste</th>
                <th>Gearbeitete NEF-Dienste</th>
                <th>Geplante NEF-Dienste</th>
            </thead>
            <tbody>
                @foreach($table as $row)
                    <tr>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->worked_nights }}</td>
                        <td>{{ $row->planned_nights }}</td>
                        <td>{{ $row->worked_nefs }}</td>
                        <td>{{ $row->planned_nefs }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
@endsection
