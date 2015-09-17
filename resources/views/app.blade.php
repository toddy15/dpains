<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dienstplan AN AKA</title>
        <link href="{{ asset(elixir('css/app.css')) }}" rel="stylesheet">
    </head>
    <body>
        @include('partials.nav')
        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @include('partials.info')
            @yield('content')
        </div>

        <script src="{{ asset(elixir('js/app.js')) }}"></script>
    </body>
</html>
