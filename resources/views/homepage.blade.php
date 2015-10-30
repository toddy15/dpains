@extends('app')

@section('content')
    <h1>Dienstplan AN</h1>
    <p>Hier geht es zum <a href="https://webmail.dienstplan-an.de/">Webmail-Service</a>.</p>

    <p>
        Um Zugriff auf die Auswertungen der Dienste zu bekommen,
        kannst Du Dich freischalten lassen. Hierfür einfach in das
        Feld die E-Mail-Adresse eintragen und einen neuen
        Zugriffscode anfordern.
    </p>

    {!! Form::open(['action' => 'PersonInfoController@requestNewHashPerMail']) !!}

    <!-- E-Mail Form Input  -->
    <div class="form-group">
        {!! Form::label('email', 'E-Mail:', ['class' => 'control-label']) !!}
        {!! Form::text('email', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Send Form -->
    <div class="form-group text-center">
        {!! Form::submit('Freischalten', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}
@endsection
