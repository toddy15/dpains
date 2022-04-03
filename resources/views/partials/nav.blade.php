<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand"
           href="{{ action('App\Http\Controllers\AnonController@homepage', isset($hash) ? $hash : '') }}">Home</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            @unless (Auth::guest())
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarAuswertungen" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Auswertungen
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarAuswertungen">
                            <li><a class="dropdown-item"
                                   href="{{ url('report/' . \App\Dpains\Helper::getPlannedYear()) }}">Jahre</a></li>
                            <li><a class="dropdown-item" href="{{ url('report/' . date('Y/m')) }}">Monate</a></li>
                            <li><a class="dropdown-item"
                                   href="{{ url('report/buandcon/' . \App\Dpains\Helper::getPlannedYear()) }}">BU und
                                    Con</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarVerwaltung" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Verwaltung
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarVerwaltung">
                            <li {!! Request::is('rawplan') ? 'class="active"' : '' !!}><a class="dropdown-item"
                                                                                          href="{{ url('rawplan') }}">Dienstpläne</a>
                            </li>
                            <li {!! Request::is('employee') ? 'class="active"' : '' !!}><a class="dropdown-item"
                                                                                           href="{{ url('employee') }}">Mitarbeiter</a>
                            </li>
                            <li {!! Request::is('comment') ? 'class="active"' : '' !!}><a class="dropdown-item"
                                                                                          href="{{ url('comment') }}">Bemerkungen</a>
                            </li>
                            <li {!! Request::is('staffgroup') ? 'class="active"' : '' !!}><a
                                    class="dropdown-item" href="{{ url('staffgroup') }}">Mitarbeitergruppen</a>
                            </li>
                            <li {!! Request::is('due_shift') ? 'class="active"' : '' !!}><a
                                    class="dropdown-item" href="{{ url('due_shift') }}">Sollzahlen</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ url('employee/month/' . date('Y/m')) }}">Monatsübersichten</a>
                            </li>
                            <li><a class="dropdown-item"
                                   href="{{ url('employee/vk/all/' . \App\Dpains\Helper::getPlannedYear()) }}">Jahresübersichten
                                    VK</a></li>
                            <li><a class="dropdown-item"
                                   href="{{ url('employee/vk/night/' . \App\Dpains\Helper::getPlannedYear()) }}">Jahresübersichten
                                    VK Nächte</a></li>
                            <li><a class="dropdown-item"
                                   href="{{ url('employee/vk/nef/' . \App\Dpains\Helper::getPlannedYear()) }}">Jahresübersichten
                                    VK NEF</a></li>
                        </ul>
                    </li>
                    {{-- Add items for anonymous access, if hash is available --}}
                    @if (isset($hash) and !empty($hash))
                        <li class="nav-item" {!! Request::is('anon/episodes/' . $hash) ? 'class="active"' : '' !!}><a
                                class="nav-link" href="{{ url('anon/episodes/' . $hash) }}">Einträge</a></li>
                        <li class="nav-item" {!! Request::is('anon/' . date('Y') . '/' . $hash) ? 'class="active"' : '' !!}>
                            <a
                                class="nav-link"
                                href="{{ url('anon/' . date('Y') . '/' . $hash) }}">Jahresauswertungen</a></li>
                    @endif
                </ul>
            @endunless
            @unless (Auth::guest())
                <a class="nav-link dropdown-toggle" href="#" id="navbarUser" role="button"
                   data-bs-toggle="dropdown" aria-expanded="false">
                    {{ Auth::user()->name }}
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarUser">
                    <li><a href="{{ url('/logout') }}">Logout</a></li>
                </ul>
            @else
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="{{ url('/login') }}">Login</a></li>
                </ul>
            @endunless
        </div>
    </div>
</nav>
