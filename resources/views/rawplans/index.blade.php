@extends('layouts.app')

@section('content')
    <h1>Dienstpläne</h1>
    <p>
        <a class="btn btn-primary" href="{{ action('App\Http\Controllers\RawplanController@create') }}">Neuen Dienstplan
            hochladen</a>
    </p>

    {!! Form::open(['action' => 'App\Http\Controllers\RawplanController@setAnonReportMonth', 'method' => 'put']) !!}

    <div class="row">
        {!! Form::label('month', 'Anonyme Auswertung bis einschließlich Monat:', ['class' => 'col-sm-3 col-form-label']) !!}
        <div class="col-sm-3">
            {!! Form::selectMonth('month', $current_anon_month, ['class' => 'form-select']) !!}
        </div>
        {!! Form::label('year', 'Jahr:', ['class' => 'visually-hidden col-form-label']) !!}
        <div class="col-sm-3">
            {!! Form::selectYear('year', $start_year, $end_year, $current_anon_year, ['class' => 'form-select']) !!}
        </div>
        <div class="col-sm-3">
            {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>

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
            <tr class="table-success">
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
