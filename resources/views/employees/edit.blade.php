@extends('layouts.app')

@section('content')
    <h1>{{ $employee->name }}</h1>

    {!! Form::model($employee, ['method' => 'PUT', 'action' => ['App\Http\Controllers\EmployeeController@update', $employee->id]]) !!}

    <!-- email Form Input  -->
    <div class="form-group {{ $errors->has('email') ? 'has-error has-feedback' : '' }}">
        {!! Form::label('email', 'E-Mail:', ['class' => 'form-label']) !!}
        {!! Form::text('email', null, ['class' => 'form-control']) !!}
        @if ($errors->has('email'))
            <span class="glyphicon glyphicon-remove form-control-feedback"></span>
        @endif
    </div>

    <!-- BU Form Input  -->
    <div class="form-group {{ $errors->has('bu_start') ? 'has-error has-feedback' : '' }}">
        {!! Form::label('bu_start', 'BU-Beginn:', ['class' => 'form-label']) !!}
        {!! Form::select('bu_start', $bu, null, ['class' => 'form-select']) !!}
        @if ($errors->has('bu_start'))
            <span class="glyphicon glyphicon-remove form-control-feedback"></span>
        @endif
    </div>

    <div class="form-group text-center">
        <!-- Speichern Form Input  -->
        {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
        <!-- Cancel Button -->
        <a class="btn btn-secondary" href="{{ action('App\Http\Controllers\EmployeeController@index') }}">Abbrechen</a>
    </div>

    {!! Form::close() !!}
@endsection
