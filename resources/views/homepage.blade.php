@extends('app')

@section('content')
    <h1>Dienstplan AN</h1>
    <p>Hier geht es zum <a href="https://webmail.dienstplan-an.de/">Webmail-Service</a>.</p>

    @if ($hash)
        <!-- Show logout button -->
        <a role="button" class="btn btn-default" href="{{ action('AnonController@logout') }}">Abmelden</a>
    @else
        <p>
            Um Zugriff auf die Auswertungen der Dienste zu bekommen,
            kannst Du Dich freischalten lassen. Hierfür einfach in das
            Feld die E-Mail-Adresse eintragen und einen neuen
            Zugriffscode anfordern.
        </p>

        {!! Form::open(['action' => 'AnonController@requestNewHashPerMail']) !!}

        <!-- E-Mail Form Input  -->
        <div class="form-group">
            {!! Form::label('email', 'E-Mail:', ['class' => 'control-label']) !!}
            {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'name@dienstplan-an.de']) !!}
        </div>

        <!-- Send Form -->
        <div class="form-group text-center">
            {!! Form::submit('Freischalten', ['class' => 'btn btn-primary']) !!}
        </div>

        {!! Form::close() !!}
    @endif
@endsection
