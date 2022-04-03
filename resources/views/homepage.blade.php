@extends('layouts.app')

@section('content')
    <h1>Dienstplan AN</h1>
    <p>Hier geht es zum <a href="https://mail.asklepios.com/">Asklepios Webmail-Service</a>.</p>

    @if ($hash)
        <!-- Show logout button -->
        <a class="btn btn-primary" href="{{ action('App\Http\Controllers\AnonController@logout', $hash) }}">Abmelden</a>
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
            {!! Form::open(['action' => 'App\Http\Controllers\AnonController@requestNewHashPerMail']) !!}

            <div class="row mb-4">
                {!! Form::label('email', 'E-Mail:', ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <div class="input-group">
                        {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'name', 'autofocus' => 'autofocus', 'aria-label'=> 'Recipient\'s username', 'aria-describedby'=>'domain-addon']) !!}
                        <span class="input-group-text" id="domain-addon">@asklepios.com</span>
                    </div>
                </div>
            </div>

            {!! Form::submit('Freischalten', ['class' => 'btn btn-primary']) !!}

            {!! Form::close() !!}
        </div>
    @endif
@endsection
