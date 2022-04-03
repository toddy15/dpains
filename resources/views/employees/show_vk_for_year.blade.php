@extends('layouts.app')

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
    <nav aria-label="Navigation des Jahres">
        <ul class="pagination">
            <li class="page-item {{ empty($previous_year_url) ? 'disabled' : '' }}"><a class="page-link" href="{{ $previous_year_url }}"><span aria-hidden="true">&larr;</span>
                    Vorheriges Jahr</a></li>
            <li class="page-item"><a class="page-link" href="{{ $next_year_url }}">Nächstes Jahr <span aria-hidden="true">&rarr;</span></a></li>
        </ul>
    </nav>
    @foreach($staffgroups as $staffgroup => $employees)
        <h3>{{ $staffgroup }}</h3>
        <table class="table table-striped">
            <thead>
                <th width="20%">Name</th>
                <th width="7%">Jan</th>
                <th width="7%">Feb</th>
                <th width="7%">Mär</th>
                <th width="7%">Apr</th>
                <th width="7%">Mai</th>
                <th width="7%">Jun</th>
                <th width="7%">Jul</th>
                <th width="7%">Aug</th>
                <th width="7%">Sep</th>
                <th width="7%">Okt</th>
                <th width="7%">Nov</th>
                <th width="7%">Dez</th>
            </thead>
            <tbody>
            @foreach($employees as $employee)
                <tr>
                    <td>{{ $employee['name'] }}</td>
                    @for ($month = 1; $month <= 12; $month++)
                        <td {!! $employee['months'][$month]['changed'] ? 'class="info"' : '' !!}>{{ $employee['months'][$month]['vk'] }}</td>
                    @endfor
                </tr>
            @endforeach
                <tr class="success">
                    <td><strong>Summe</strong></td>
                    @for ($month = 1; $month <= 12; $month++)
                        <td><strong>{{ $vk_per_month[$staffgroup][$month] }}</strong></td>
                    @endfor
                </tr>
                <tr class="success">
                    <td colspan="12"><strong>Jahresmittel</strong></td>
                    <td><strong>{{ $vk_per_month[$staffgroup]['yearly_mean'] }}</strong></td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <h3>Gesamt-VK</h3>
    <table class="table table-striped">
        <thead>
        <th width="20%">Name</th>
        <th width="7%">Jan</th>
        <th width="7%">Feb</th>
        <th width="7%">Mär</th>
        <th width="7%">Apr</th>
        <th width="7%">Mai</th>
        <th width="7%">Jun</th>
        <th width="7%">Jul</th>
        <th width="7%">Aug</th>
        <th width="7%">Sep</th>
        <th width="7%">Okt</th>
        <th width="7%">Nov</th>
        <th width="7%">Dez</th>
        </thead>
        <tbody>
        <tr class="success">
            <td><strong>Summe</strong></td>
            @for ($month = 1; $month <= 12; $month++)
                <td><strong>{{ $vk_per_month['all'][$month] }}</strong></td>
            @endfor
        </tr>
        <tr class="success">
            <td colspan="12"><strong>Jahresmittel</strong></td>
            <td><strong>{{ $vk_per_month['all']['yearly_mean'] }}</strong></td>
        </tr>
        </tbody>
    </table>
@endsection
