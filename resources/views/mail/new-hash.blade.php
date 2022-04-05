@component('mail::message')
# Neuer Zugriffscode

Hallo,

unter dieser URL kannst Du Deine Daten einsehen:

@component('mail::button', ['url' => $url])
Auswertung ansehen
@endcomponent

Schönen Gruß<br>
{{ config('app.name') }}
@endcomponent
