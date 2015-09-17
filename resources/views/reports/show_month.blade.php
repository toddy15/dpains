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
    @if (empty($results))
        <p>Keine Daten für diesen Monat.</p>
    @else
        <table class="table table-striped">
            <thead>
                <th>Name</th>
                <th>Nachtdienste</th>
                <th>NEFs</th>
            </thead>
            <tbody>
            @foreach($results as $result)
                <tr>
                    <td>{{ $result->name }}</td>
                    <td>{{ $result->shifts->nights }}</td>
                    <td>{{ $result->shifts->nefs }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection
