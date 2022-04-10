@extends('layouts.app')

@section('content')
    <h1>Dienstplan hochladen</h1>

    {!! Form::open(['action' => 'App\Http\Controllers\RawplanController@store']) !!}

    <div class="row">
        <x-label for="month" value="Monat:" class="col-sm-4 col-form-label"/>
        <div class="col-sm-4">
            <select id="month" name="month" class="form-select" aria-label="Monat">
                @foreach($month_names as $number => $month_name)
                    <option value="{{ $number }}" {{ old('month', $selected_month) == $number ? 'selected' : '' }}>{{ $month_name }}</option>
                @endforeach
            </select>
        </div>

        <x-label for="year" value="Jahr:" class="visually-hidden col-sm-4 col-form-label"/>
        <div class="col-sm-4">
            <select id="year" name="year" class="form-select" aria-label="Jahr">
                @for($y=$start_year; $y <= $end_year; $y++)
                    <option {{ old('year', $selected_year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>

    <x-label for="people" value="Mitarbeiter:"/>
    <x-textarea id="people" name="people" cols="50" rows="10" invalid="{{ $errors->has('people') }}">
        {{ old('people') }}
    </x-textarea>

    <x-label for="shifts" value="Schichten:"/>
    <x-textarea id="shifts" name="shifts" cols="50" rows="10" invalid="{{ $errors->has('shifts') }}">
        {{ old('shifts') }}
    </x-textarea>

    <div class="form-group text-center mt-4">
        <x-button>Speichern</x-button>
        <x-link-button href="{{ route('rawplans.index') }}" class="btn-secondary">Abbrechen</x-link-button>
    </div>

    {!! Form::close() !!}
@endsection
