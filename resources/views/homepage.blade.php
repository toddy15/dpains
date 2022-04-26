@extends('layouts.app')

@section('content')
    <h1>Dienstplan AN</h1>
    <p>Hier geht es zum <a href="https://mail.asklepios.com/owa/">Asklepios Webmail-Service</a>.</p>

    @if ($hash)
        <!-- Show logout button -->
        <x-link-button href="{{ action('App\Http\Controllers\AnonController@logout', $hash) }}" class="btn btn-primary">
            Abmelden
        </x-link-button>
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
            <form action="{{ route('anon.newHash') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <x-label for="email" value="E-Mail:" class="col-sm-2 col-form-label" />
                    <div class="col-sm-10">
                        <div class="input-group">
                            <x-input value="{{ old('email') }}" name="email" id="email" required autofocus
                                placeholder="name" aria-label="E-Mail des Benutzers" aria-describedby="domain-addon" />
                            <span class="input-group-text" id="domain-addon">@asklepios.com</span>
                        </div>
                    </div>
                </div>
                <x-button>Freischalten</x-button>
            </form>

        </div>
    @endif
@endsection
