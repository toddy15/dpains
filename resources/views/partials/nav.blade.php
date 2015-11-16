<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ action('AnonController@homepage', isset($hash) ? $hash : '') }}">Home</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                @unless (Auth::guest())
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Auswertungen <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('report/' . date('Y')) }}">Jahre</a></li>
                            <li><a href="{{ url('report/' . date('Y/m')) }}">Monate</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Verwaltung <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li {!! Request::is('rawplan') ? 'class="active"' : '' !!}><a href="{{ url('rawplan') }}">Dienstpläne</a></li>
                            <li {!! Request::is('employee') ? 'class="active"' : '' !!}><a href="{{ url('employee') }}">Mitarbeiter</a></li>
                            <li {!! Request::is('comment') ? 'class="active"' : '' !!}><a href="{{ url('comment') }}">Bemerkungen</a></li>
                            <li {!! Request::is('staffgroup') ? 'class="active"' : '' !!}><a href="{{ url('staffgroup') }}">Mitarbeitergruppen</a></li>
                            <li><a href="{{ url('employee/month/' . date('Y/m')) }}">Monatsübersichten</a></li>
                            <li {!! Request::is('backup') ? 'class="active"' : '' !!}><a href="{{ url('backup') }}">Backup</a></li>
                        </ul>
                    </li>
                @endunless
                {{-- Add items for anonymous access, if hash is available --}}
                @if (isset($hash) and !empty($hash))
                    <li {!! Request::is('anon/episodes/' . $hash) ? 'class="active"' : '' !!}><a href="{{ url('anon/episodes/' . $hash) }}">Einträge</a></li>
                    {{-- @TODO: Do not hardcode --}}
                    <li {!! Request::is('anon/2015/' . $hash) ? 'class="active"' : '' !!}><a href="{{ url('anon/2015/' . $hash) }}">Auswertung 2015</a></li>
                @endif
            </ul>

            @unless (Auth::guest())
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            @else
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="{{ url('/auth/login') }}">Login</a></li>
                </ul>
            @endunless
        </div>
    </div>
</nav>
