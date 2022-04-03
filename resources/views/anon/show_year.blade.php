@extends('layouts.app')
@inject('helper', 'App\Dpains\Helper')

@section('content')
    <h1>Auswertung für {{ $year }}</h1>
    <nav aria-label="Navigation des Jahres">
        <ul class="pagination">
            <li class="page-item {{ empty($previous_year_url) ? 'disabled' : '' }}"><a class="page-link" href="{{ $previous_year_url }}"><span aria-hidden="true">&larr;</span>
                    Vorheriges Jahr</a></li>
            <li class="page-item"><a class="page-link" href="{{ $next_year_url }}">Nächstes Jahr <span aria-hidden="true">&rarr;</span></a></li>
        </ul>
    </nav>
    @if ($readable_worked_month)
        <h2>Gearbeitet bis Ende {{ $readable_worked_month }}, geplant bis Ende {{ $readable_planned_month }}</h2>
    @else
        <h2>Geplant bis Ende {{ $readable_planned_month }}</h2>
    @endif
    <p>Stand der Auswertung: {{ $latest_change }}</p>
    @foreach($tables as $staffgroup => $table)
        <h3>{{ $staffgroup }}</h3>
        <p>
            Sollzahl Nächte pro Jahr: {{ $table['due_nights'] }}<br />
            Sollzahl NEF-Dienste pro Jahr: {{ $table['due_nefs'] }}
        </p>
        <table class="table table-striped">
            <thead>
                <th>Name</th>
                <th>Gearb. Nächte</th>
                <th>Gepl. Nächte</th>
                <th>{!! $helper->sortTableBy('diff_planned_nights', 'Abweichung', $year, $hash) !!}</th>
                <th>Gearb. NEF</th>
                <th>Gepl. NEF</th>
                <th>{!! $helper->sortTableBy('diff_planned_nefs', 'Abweichung', $year, $hash) !!}</th>
            </thead>
            <tbody>
                @foreach($table['rows'] as $rows)
                    @foreach($rows as $row)
                        <tr {!! isset($row->highlight_row) ? 'class="info"' : '' !!}>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->worked_nights }}</td>
                            <td>{{ $row->planned_nights }}</td>
                            <td>{{ $row->diff_planned_nights }}</td>
                            <td>{{ $row->worked_nefs }}</td>
                            <td>{{ $row->planned_nefs }}</td>
                            <td>{{ $row->diff_planned_nefs }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endforeach
@endsection
