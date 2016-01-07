@extends('app')

@section('content')
    <h1>
        Übersicht der VK für {{ $year }}
        @if ($which_vk == 'night')
            (Nächte)
        @endif
        @if ($which_vk == 'nef')
            (NEF)
        @endif
    </h1>
    <nav>
        <ul class="pager">
            @if (empty($previous_year_url))
                <li class="previous disabled"><span aria-hidden="true">&larr; Vorheriges Jahr</span></li>
            @else
                <li class="previous"><a href="{{ $previous_year_url }}"><span aria-hidden="true">&larr;</span> Vorheriges Jahr</a></li>
            @endif
            <li class="next"><a href="{{ $next_year_url }}">Nächstes Jahr <span aria-hidden="true">&rarr;</span></a></li>
        </ul>
    </nav>
    <h2>Mitarbeiter</h2>
    <table class="table table-striped">
        <thead>
            <th>Name</th>
            <th>Gruppe</th>
            <th>Jan</th>
            <th>Feb</th>
            <th>Mär</th>
            <th>Apr</th>
            <th>Mai</th>
            <th>Jun</th>
            <th>Jul</th>
            <th>Aug</th>
            <th>Sep</th>
            <th>Okt</th>
            <th>Nov</th>
            <th>Dez</th>
        </thead>
        <tbody>
        @foreach($employees as $employee)
            <tr>
                <td>{{ $employee['name'] }}</td>
                <td>{{ $employee['staffgroup'] }}</td>
                @for ($month = 1; $month <= 12; $month++)
                    <td {!! $employee['months'][$month]['changed'] ? 'class="info"' : '' !!}>{{ $employee['months'][$month]['vk'] }}</td>
                @endfor
            </tr>
        @endforeach
            <tr class="success">
                <td colspan="2"><strong>Summe</strong></td>
                @for ($month = 1; $month <= 12; $month++)
                    <td><strong>{{ $vk_per_month[$month] }}</strong></td>
                @endfor
            </tr>
        </tbody>
    </table>
@endsection
