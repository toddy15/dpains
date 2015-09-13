@extends('app')

@section('content')
    <h1>Dienstplan hochladen</h1>

    {!! Form::open(['action' => 'RawplanController@store']) !!}

    <!-- Month Form Input  -->
    <div class="form-group">
        {!! Form::label('month', 'Monat:', ['class' => 'control-label']) !!}
        <div class="form-inline">
            {!! Form::selectMonth('month', date('m'), ['class' => 'form-control']) !!}
            {!! Form::label('year', 'Jahr:', ['class' => 'sr-only control-label']) !!}
            {!! Form::selectYear('year', $start_year, $end_year, date('Y'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <!-- People Form Input  -->
    <div class="form-group {{ $errors->has('people') ? 'has-error has-feedback' : '' }}">
        {!! Form::label('people', 'Mitarbeiter:', ['class' => 'control-label']) !!}
        {!! Form::textarea('people', null, ['class' => 'form-control']) !!}
        @if ($errors->has('people'))
            <span class="glyphicon glyphicon-remove form-control-feedback"></span>
        @endif
    </div>

    <!-- Shifts Form Input  -->
    <div class="form-group {{ $errors->has('shifts') ? 'has-error has-feedback' : '' }}">
        {!! Form::label('shifts', 'Schichten:', ['class' => 'control-label']) !!}
        {!! Form::textarea('shifts', null, ['class' => 'form-control']) !!}
        @if ($errors->has('shifts'))
            <span class="glyphicon glyphicon-remove form-control-feedback"></span>
        @endif
    </div>

    <div class="form-group text-center">
        <!-- Speichern Form Input  -->
        {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
        <!-- Cancel Button -->
        <a class="btn btn-danger" href="{{ action('RawplanController@index') }}">Abbrechen</a>
    </div>

    {!! Form::close() !!}
@endsection
