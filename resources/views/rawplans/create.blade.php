@extends('layouts.app')

@section('content')
    <h1>Dienstplan hochladen</h1>

    <form action="{{ route('rawplans.store') }}" method="POST">
        @csrf

        <div class="row">
            <x-label for="month" value="Monat:" class="col-sm-4 col-form-label" />
            <div class="col-sm-4">
                <select id="month" name="month" class="form-select" aria-label="Monat">
                    @foreach ($month_names as $number => $month_name)
                        <option value="{{ $number }}" @selected(old('month', $selected_month) == $number)>{{ $month_name }}</option>
                    @endforeach
                </select>
            </div>

            <x-label for="year" value="Jahr:" class="visually-hidden col-sm-4 col-form-label" />
            <div class="col-sm-4">
                <select id="year" name="year" class="form-select" aria-label="Jahr">
                    @for ($y = $start_year; $y <= $end_year; $y++)
                        <option @selected(old('year', $selected_year) == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <x-label for="people" value="Mitarbeiter:" />
        <x-textarea id="people" name="people" cols="50" rows="10" autofocus required
            invalid="{{ $errors->has('people') }}" value="{{ old('people') }}" />

        <x-label for="shifts" value="Schichten:" />
        <x-textarea id="shifts" name="shifts" cols="50" rows="10" required invalid="{{ $errors->has('shifts') }}"
            value="{{ old('shifts') }}" />

        <div class="form-group text-center mt-4">
            <x-button>Speichern</x-button>
            <x-link-button href="{{ route('rawplans.index') }}" class="btn-secondary">Abbrechen</x-link-button>
        </div>

    </form>
@endsection
