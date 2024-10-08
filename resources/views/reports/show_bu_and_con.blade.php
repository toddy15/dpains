@extends('layouts.app')

@section('content')
    <h1>BU und Con {{ $year }}</h1>
    <nav aria-label="Navigation des Jahres">
        <ul class="pagination">
            <li class="page-item {{ empty($previous_year_url) ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $previous_year_url }}"><span aria-hidden="true">&larr;</span>Vorheriges
                    Jahr</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ $next_year_url }}">Nächstes Jahr <span aria-hidden="true">&rarr;</span></a>
            </li>
        </ul>
    </nav>
    <table class="table table-striped">
        <thead>
            <tr>
                <th rowspan="2">Name</th>
                <th rowspan="2">BU-Beginn</th>
                <th colspan="2" class="text-center">{{ $year - 1 }}</th>
                <th colspan="2" class="text-center">{{ $year }}</th>
                <th colspan="2" class="text-center">{{ $year + 1 }}</th>
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
            @foreach ($employees as $employee)
                <tr>
                    <td>{{ $employee['name'] }}</td>
                    @if ($employee['bu_cleartext'] == 'Nicht hinterlegt')
                        <td class="table-danger">{{ $employee['bu_cleartext'] }}</td>
                    @else
                        <td>{{ $employee['bu_cleartext'] }}</td>
                    @endif
                    @foreach ($employee['data'] as $buandcon)
                        <td>{{ $buandcon['bus'] }}</td>
                        @if ((int) $buandcon['cons'] > 3)
                            <td class="table-danger">{{ $buandcon['cons'] }}</td>
                        @else
                            <td>{{ $buandcon['cons'] }}</td>
                        @endif
                    @endforeach
                    @if ((int) $employee['sum'] > 10)
                        <td class="table-danger">{{ $employee['sum'] }}</td>
                    @else
                        <td>{{ $employee['sum'] }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
