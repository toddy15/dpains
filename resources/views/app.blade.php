<!doctype html>
<html lang="de">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">

    <title>Dienstplan AN AKA</title>
</head>
<body>
@include('partials.nav')
<div class="container">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
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

<script src="{{ asset(mix('js/app.js')) }}"></script>
</body>
</html>
