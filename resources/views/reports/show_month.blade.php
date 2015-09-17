@extends('app')

@section('content')
    <h1>{{ $readable_month }}</h1>
    <nav>
        <ul class="pager">
            @if (empty($previous_month_url))
                <li class="previous disabled"><span aria-hidden="true">&larr; Vorheriger Monat</span></li>
            @else
                <li class="previous"><a href="{{ $previous_month_url }}"><span aria-hidden="true">&larr;</span> Vorheriger Monat</a></li>
            @endif
            <li class="next"><a href="{{ $next_month_url }}">Nächster Monat <span aria-hidden="true">&rarr;</span></a></li>
        </ul>
    </nav>
    <table class="table table-striped">
        <thead>
            <th>Name</th>
            <th>Nachtdienste</th>
            <th>NEFs</th>
        </thead>
        <tbody>
        @foreach($reports as $report)
            <tr>
                <td>{{ $names[$report->number] }}</td>
                <td>{{ $report->nights }}</td>
                <td>{{ $report->nefs }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
