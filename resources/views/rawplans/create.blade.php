@extends('layouts.app')

@section('content')
    <h1>Dienstplan hochladen</h1>

    {!! Form::open(['action' => 'App\Http\Controllers\RawplanController@store']) !!}

    <!-- Month Form Input  -->
    <div class="row">
        {!! Form::label('month', 'Monat:', ['class' => 'col-sm-4 col-form-label']) !!}
        <div class="col-sm-4">
            {!! Form::selectMonth('month', $selected_month, ['class' => 'form-select']) !!}
        </div>
        {!! Form::label('year', 'Jahr:', ['class' => 'visually-hidden col-sm-4 col-form-label']) !!}
        <div class="col-sm-4">
            {!! Form::selectYear('year', $start_year, $end_year, $selected_year, ['class' => 'form-select']) !!}
        </div>
    </div>

    {{--    <div class="form-group {{ $errors->has('people') ? 'has-error has-feedback' : '' }}">--}}
    {!! Form::label('people', 'Mitarbeiter:', ['class' => 'form-label']) !!}
    {!! Form::textarea('people', null, ['class' => 'form-control']) !!}
    {{--    </div>--}}

    {{--    <div class="form-group {{ $errors->has('shifts') ? 'has-error has-feedback' : '' }}">--}}
    {!! Form::label('shifts', 'Schichten:', ['class' => 'form-label']) !!}
    {!! Form::textarea('shifts', null, ['class' => 'form-control']) !!}
    {{--    </div>--}}

    <div class="text-center">
        {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
        <a class="btn btn-secondary" href="{{ action('App\Http\Controllers\RawplanController@index') }}">Abbrechen</a>
    </div>

    {!! Form::close() !!}
@endsection
