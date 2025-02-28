@extends('layouts.app')

@section('content')
    <h1>Dienstpläne</h1>
    <p>
        <x-link-button class="btn-primary" href="{{ route('rawplans.create') }}">
            Neuen Dienstplan hochladen
        </x-link-button>
    </p>

    <form action="{{ route('rawplans.setAnonReportMonth') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <x-label for="month" value="Anonyme Auswertung bis einschließlich Monat:" class="col-sm-3 col-form-label" />
            <div class="col-sm-3">
                <select id="month" name="month" class="form-select" aria-label="Monat">
                    @foreach ($month_names as $number => $month_name)
                        <option value="{{ $number }}" @selected(old('month', $current_anon_month) == $number)>
                            {{ $month_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <x-label for="year" value="Jahr:" class="visually-hidden col-sm-3 col-form-label" />
            <div class="col-sm-3">
                <select id="year" name="year" class="form-select" aria-label="Jahr">
                    @for ($y = $start_year; $y <= $end_year; $y++)
                        <option @selected(old('year', $current_anon_year) == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-sm-3">
                <x-button>Speichern</x-button>
            </div>
        </div>
    </form>

    <table class="table table-striped">
        <thead>
            <th>Monat</th>
            <th>Aktualisiert</th>
            <th>Auswertung für anonymen Zugriff</th>
            <th>Aktion</th>
        </thead>
        <tbody>
            @foreach ($rawplans_planned as $rawplan)
                <tr>
                    <td>{{ $rawplan->month }}</td>
                    <td>
                        {{ Carbon\Carbon::parse($rawplan->updated_at)->timezone('Europe/Berlin')->locale('de')->isoFormat('Do MMMM YYYY, HH:mm:ss') }}
                    </td>
                    <td>{{ $rawplan->anon_report ? 'Ja' : 'Nein' }}</td>
                    <td>
                        <form action="{{ route('rawplans.destroy', $rawplan) }}">
                            @csrf
                            @method('DELETE')

                            <x-button class="btn-danger">Löschen</x-button>
                        </form>
                    </td>
                </tr>
            @endforeach
            @foreach ($rawplans_worked as $rawplan)
                <tr class="table-success">
                    <td>{{ $rawplan->month }}</td>
                    <td>
                        {{ Carbon\Carbon::parse($rawplan->updated_at)->timezone('Europe/Berlin')->locale('de')->isoFormat('Do MMMM YYYY, HH:mm:ss') }}
                    </td>
                    <td>{{ $rawplan->anon_report ? 'Ja' : 'Nein' }}</td>
                    <td>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
