@extends('app')

@section('content')
    {!! Form::open() !!}

    <!-- Name Form Input  -->
    <div class="form-group">
        {!! Form::label('name', 'Name:', ['class' => 'control-label']) !!}
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Start_date Form Input  -->
    <div class="form-group">
        {!! Form::label('start_date', 'Beginnt im Monat:', ['class' => 'control-label']) !!}
        {!! Form::text('start_date', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Speichern Form Input  -->
    <div class="form-group">
        {!! Form::submit('Speichern', ['class' => 'btn btn-primary form-control']) !!}
    </div>

    {!! Form::close() !!}
@endsection
