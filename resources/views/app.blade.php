<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dienstplan AN AKA</title>
        <link href="{{ asset(elixir('css/app.css')) }}" rel="stylesheet">
        <meta name="MSSmartTagsPreventParsing" content="TRUE">
    </head>
    <body>
        <div class="container">
            @yield('content')
        </div>

        <script src="{{ asset(elixir('js/app.js')) }}"></script>
    </body>
</html>
