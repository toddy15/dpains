@extends('layouts.app')

@section('content')
    <h1>{{ $readable_month }}</h1>
    <nav aria-label="Navigation des Monats">
        <ul class="pagination">
            <li class="page-item {{ empty($previous_month_url) ? 'disabled' : '' }}"><a class="page-link"
                    href="{{ $previous_month_url }}"><span aria-hidden="true">&larr;</span>
                    Vorheriger Monat</a></li>
            <li class="page-item"><a class="page-link" href="{{ $next_month_url }}">Nächster Monat <span
                        aria-hidden="true">&rarr;</span></a></li>
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
                <th>BU</th>
                <th>Con</th>
            </thead>
            <tbody>
                @foreach ($results as $result)
                    <tr>
                        <td>{{ $result->name }}</td>
                        <td>{{ $result->shifts->nights }}</td>
                        <td>{{ $result->shifts->nefs }}</td>
                        <td>{{ $result->shifts->bus }}</td>
                        <td>{{ $result->shifts->cons }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
