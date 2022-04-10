@extends('layouts.app')

@section('content')
    <h1>Dienstplan hochladen</h1>

    {!! Form::open(['action' => 'App\Http\Controllers\RawplanController@store']) !!}

    <!-- Month Form Input  -->
    <div class="row">
        {!! Form::label('month', 'Monat:', ['class' => 'col-sm-4 col-form-label']) !!}
        <div class="col-sm-4">
            {!! Form::selectMonth('month', $selected_month, ['class' => 'form-select']) !!}
        </div>
        {!! Form::label('year', 'Jahr:', ['class' => 'visually-hidden col-sm-4 col-form-label']) !!}
        <div class="col-sm-4">
            {!! Form::selectYear('year', $start_year, $end_year, $selected_year, ['class' => 'form-select']) !!}
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
        <x-link-button href="{{ route('rawplan.index') }}" class="btn-secondary">Abbrechen</x-link-button>
    </div>

    {!! Form::close() !!}
@endsection
