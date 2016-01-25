@extends('app')

@section('content')
    <h1>Dienstpläne</h1>
    <p>
        <a class="btn btn-primary" href="{{ action('RawplanController@create') }}">Neuen Dienstplan hochladen</a>
    </p>
    <table class="table table-striped">
        <thead>
            <th>Monat</th>
            <th>Aktualisiert</th>
            <th>Auswertung für anonymen Zugriff</th>
            <th>Aktion</th>
        </thead>
        <tbody>
        @foreach($rawplans_planned as $rawplan)
            <tr>
                <td>{{ $rawplan->month }}</td>
                <td>{{ $rawplan->updated_at }}</td>
                <td>{{ $rawplan->anon_report ? 'Ja' : 'Nein' }}</td>
                <td>
                    {!! Form::open(['action' => ['RawplanController@destroy', $rawplan->id], 'method' => 'delete']) !!}
                    {!! Form::submit('Löschen', ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                    {!! Form::open(['action' => ['RawplanController@flipAnonReport', $rawplan->id], 'method' => 'put']) !!}
                    {!! Form::submit('Anonyme Auswertung ändern', ['class' => 'btn btn-primary']) !!}
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        @foreach($rawplans_worked as $rawplan)
            <tr class="success">
                <td>{{ $rawplan->month }}</td>
                <td>{{ $rawplan->updated_at }}</td>
                <td>{{ $rawplan->anon_report ? 'Ja' : 'Nein' }}</td>
                <td>
                    {!! Form::open(['action' => ['RawplanController@destroy', $rawplan->id], 'method' => 'delete']) !!}
                    {!! Form::submit('Löschen', ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                    {!! Form::open(['action' => ['RawplanController@flipAnonReport', $rawplan->id], 'method' => 'put']) !!}
                    {!! Form::submit('Anonyme Auswertung ändern', ['class' => 'btn btn-primary']) !!}
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
