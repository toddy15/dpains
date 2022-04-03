@extends('layouts.app')

@section('content')
    <h1>Dienstpläne</h1>
    <p>
        <a class="btn btn-primary" href="{{ action('App\Http\Controllers\RawplanController@create') }}">Neuen Dienstplan hochladen</a>
    </p>

    {!! Form::open(['action' => 'App\Http\Controllers\RawplanController@setAnonReportMonth', 'method' => 'put', 'class' => 'form-inline']) !!}

    <!-- Month Form Input  -->
    <div class="form-group">
        {!! Form::label('month', 'Anonyme Auswertung bis einschließlich Monat:', ['class' => 'control-label']) !!}
        {!! Form::selectMonth('month', $current_anon_month, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('year', 'Jahr:', ['class' => 'sr-only control-label']) !!}
        {!! Form::selectYear('year', $start_year, $end_year, $current_anon_year, ['class' => 'form-control']) !!}
    </div>
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}

    {!! Form::close() !!}

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
                    {!! Form::open(['action' => ['App\Http\Controllers\RawplanController@destroy', $rawplan->id], 'method' => 'delete']) !!}
                    {!! Form::submit('Löschen', ['class' => 'btn btn-danger']) !!}
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
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
