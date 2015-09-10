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
            <th>Aktion</th>
        </thead>
        <tbody>
            @foreach($rawplans as $rawplan)
                <tr>
                    <td>{{ $rawplan->month }}</td>
                    <td>{{ $rawplan->updated_at }}</td>
                    <td>
                        {!! Form::open(['action' => ['RawplanController@destroy', $rawplan->id], 'method' => 'delete']) !!}
                        {!! Form::submit('Löschen', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
