@extends('app')

@section('content')
    <h1>Eintrag bearbeiten</h1>

    {!! Form::model($person, ['method' => 'PUT', 'action' => ['PersonInfoController@update', $person->number]]) !!}

    <!-- email Form Input  -->
    <div class="form-group {{ $errors->has('email') ? 'has-error has-feedback' : '' }}">
        {!! Form::label('email', 'E-Mail:', ['class' => 'control-label']) !!}
        {!! Form::text('email', null, ['class' => 'form-control']) !!}
        @if ($errors->has('email'))
            <span class="glyphicon glyphicon-remove form-control-feedback"></span>
        @endif
    </div>

    <div class="form-group text-center">
        <!-- Speichern Form Input  -->
        {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
                <!-- Cancel Button -->
        <a class="btn btn-default" href="{{ action('PersonInfoController@index') }}">Abbrechen</a>
    </div>

    {!! Form::close() !!}
@endsection
