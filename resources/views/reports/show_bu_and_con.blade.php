@extends('app')

@section('content')
    <h1>BU und Con {{ $year }}</h1>
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
    <table class="table table-striped">
        <thead>
        <tr>
            <th rowspan="2">Name</th>
            <th rowspan="2">BU-Beginn</th>
            <th colspan="2" style="text-align: center">{{ $year - 1 }}</th>
            <th colspan="2" style="text-align: center">{{ $year }}</th>
            <th colspan="2" style="text-align: center">{{ $year + 1 }}</th>
            <th rowspan="2">Summe</th>
        </tr>
        <tr>
            <th>BU</th>
            <th>Con</th>
            <th>BU</th>
            <th>Con</th>
            <th>BU</th>
            <th>Con</th>
        </tr>
        </thead>
        <tbody>
        @foreach($employees as $employee)
            <tr>
                <td>{{ $employee['name'] }}</td>
                <td>{{ $employee['bu_cleartext'] }}</td>
                @foreach($employee['data'] as $buandcon)
                    <td>{{ $buandcon['bus'] }}</td>
                    <td>{{ $buandcon['cons'] }}</td>
                @endforeach
                <td>{{ $employee['sum'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
