@extends('layouts.app')

@section('content')
    <h1>Bereitschaftsdienste {{ $year }}</h1>
    <nav aria-label="Navigation des Jahres">
        <ul class="pagination">
            <li class="page-item {{ empty($previous_year_url) ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $previous_year_url }}"><span aria-hidden="true">&larr;</span>Vorheriges
                    Jahr</a>
            </li>
            <li class="page-item {{ empty($next_year_url) ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $next_year_url }}">Nächstes Jahr <span aria-hidden="true">&rarr;</span></a>
            </li>
        </ul>
    </nav>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Jan</th>
                <th>Feb</th>
                <th>Mrz</th>
                <th>Apr</th>
                <th>Mai</th>
                <th>Jun</th>
                <th>Summe</th>
                <th>Jul</th>
                <th>Aug</th>
                <th>Sep</th>
                <th>Okt</th>
                <th>Nov</th>
                <th>Dez</th>
                <th>Summe</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employee_infos as $employee_info)
                <tr>
                    <td>{{ $employee_info['name'] }}</td>
                    @foreach ($combined_bds_first_half[$employee_info['id']] as $bd_info)
                        @if ($bd_info['markup'] === 'danger')
                            <td class="table-danger">{{ $bd_info['value'] }}</td>
                        @elseif ($bd_info['markup'] === 'warning')
                            <td class="table-warning">{{ $bd_info['value'] }}</td>
                        @else
                            <td>{{ $bd_info['value'] }}</td>
                        @endif
                    @endforeach
                    @if ($combined_sums[$employee_info['id']][0]['markup'] === 'danger')
                        <td class="table-danger">{{ $combined_sums[$employee_info['id']][0]['value'] }}</td>
                    @elseif ($combined_sums[$employee_info['id']][0]['markup'] === 'warning')
                        <td class="table-warning">{{ $combined_sums[$employee_info['id']][0]['value'] }}</td>
                    @else
                        <td>{{ $combined_sums[$employee_info['id']][0]['value'] }}</td>
                    @endif
                    @foreach ($combined_bds_second_half[$employee_info['id']] as $bd_info)
                        @if ($bd_info['markup'] === 'danger')
                            <td class="table-danger">{{ $bd_info['value'] }}</td>
                        @elseif ($bd_info['markup'] === 'warning')
                            <td class="table-warning">{{ $bd_info['value'] }}</td>
                        @else
                            <td>{{ $bd_info['value'] }}</td>
                        @endif
                    @endforeach
                    @if ($combined_sums[$employee_info['id']][1]['markup'] === 'danger')
                        <td class="table-danger">{{ $combined_sums[$employee_info['id']][1]['value'] }}</td>
                    @elseif ($combined_sums[$employee_info['id']][1]['markup'] === 'warning')
                        <td class="table-warning">{{ $combined_sums[$employee_info['id']][1]['value'] }}</td>
                    @else
                        <td>{{ $combined_sums[$employee_info['id']][1]['value'] }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
