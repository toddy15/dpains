<nav class="navbar navbar-expand-md navbar-light shadow-sm" style="background-color: #FEF08A;">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/' . ($hash ?? '')) }}">
            Home
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a id="navbarAuswertungen" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            Auswertungen
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarAuswertungen">
                            <a class="dropdown-item" href="{{ route('reports.showCurrentYear') }}">Jahre</a>
                            <a class="dropdown-item" href="{{ route('reports.showbds') }}">Jahresübersicht BD</a>
                            <a class="dropdown-item" href="{{ route('reports.showCurrentBuAndCon') }}">BU und
                                Con</a>
                            <a class="dropdown-item" href="{{ url('employees/vk/all') }}">Jahresübersichten
                                VK</a>
                            <a class="dropdown-item" href="{{ url('employees/vk/night') }}">Jahresübersichten
                                VK Nächte</a>
                            <a class="dropdown-item" href="{{ url('employees/vk/nef') }}">Jahresübersichten
                                VK NEF</a>
                            {{--                            <a class="dropdown-item" href="{{ url('report/' . date('Y/m')) }}">Monate</a> --}}
                            {{--                            <a class="dropdown-item" --}}
                            {{--                                href="{{ url('employees/month/' . date('Y/m')) }}">Monatsübersichten</a> --}}
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a id="navbarVerwaltung" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            Verwaltung
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarVerwaltung">
                            <a class="dropdown-item" href="{{ route('rawplans.index') }}">Dienstpläne</a>
                            <a class="dropdown-item" href="{{ route('employees.index') }}">Mitarbeitende</a>
                            <a class="dropdown-item" href="{{ route('comments.index') }}">Bemerkungen</a>
                            <a class="dropdown-item" href="{{ route('staffgroups.index') }}">Gruppen</a>
                            <a class="dropdown-item" href="{{ route('due_shifts.index') }}">Sollzahlen</a>
                        </div>
                    </li>
                @endauth
                {{-- Add items for anonymous access, if hash is available --}}
                @if (isset($hash) and !empty($hash))
                    <li class="nav-item" {!! Request::is('anon/episodes/' . $hash) ? 'class="active"' : '' !!}><a class="nav-link"
                            href="{{ url('anon/episodes/' . $hash) }}">Einträge</a></li>
                    <li class="nav-item" {!! Request::is('anon/' . $hash) ? 'class="active"' : '' !!}>
                        <a class="nav-link" href="{{ url('anon/' . $hash) }}">Jahresauswertungen</a>
                    </li>
                @endif
            </ul>
        </div>

        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav ms-auto">
            <!-- Authentication Links -->
            @guest
                @if (Route::has('login'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                @endif

                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li>
                @endif
            @else
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Abmelden
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </div>
</nav>
