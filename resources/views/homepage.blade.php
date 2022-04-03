@extends('layouts.app')

@section('content')
    <h1>Dienstplan AN</h1>
    <p>Hier geht es zum <a href="https://mail.asklepios.com/">Asklepios Webmail-Service</a>.</p>

    @if ($hash)
        <!-- Show logout button -->
        <a role="button" class="btn btn-default" href="{{ action('App\Http\Controllers\AnonController@logout', $hash) }}">Abmelden</a>
    @else
        <p>
            Um Zugriff auf die Auswertungen der Dienste zu bekommen,
            kannst Du Dich freischalten lassen. Hierfür einfach in das
            Feld die E-Mail-Adresse eintragen und einen neuen
            Zugriffscode anfordern.
        </p>
        <p>
            Die Domain @asklepios.com muss nicht eingegeben werden.
        </p>

        <div class="text-center">
            {!! Form::open(['action' => 'App\Http\Controllers\AnonController@requestNewHashPerMail', 'class' => 'form-inline']) !!}

            <!-- E-Mail Form Input  -->
            <div class="form-group {{ $errors->has('email') ? 'has-error has-feedback' : '' }}">
                {!! Form::label('email', 'E-Mail:', ['class' => 'control-label']) !!}
                <div class="input-group">
                    {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'name', 'autofocus' => 'autofocus', 'aria-describedby' => 'domain-addon']) !!}
                    <span class="input-group-addon" id="domain-addon">@asklepios.com</span>
                </div>
            </div>

            <!-- Send Form -->
            <div class="form-group">
                {!! Form::submit('Freischalten', ['class' => 'btn btn-primary']) !!}
            </div>

            {!! Form::close() !!}
        </div>
    @endif
@endsection
