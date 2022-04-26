@component('mail::message')
# Neuer Zugriffscode

Hallo,

unter dieser URL kannst du deine Daten ansehen:

@component('mail::button', ['url' => $url])
    Auswertung ansehen
@endcomponent

Schönen Gruß<br>
{{ config('app.name') }}
@endcomponent
