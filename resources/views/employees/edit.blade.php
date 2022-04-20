@extends('layouts.app')

@section('content')
    <h1>{{ $employee->name }}</h1>

    <form action="{{ route('employees.update', $employee) }}" method="post">
        @csrf
        @method('PUT')

        <x-label for="email" value="E-Mail:" />
        <x-input name="email" id="email" value="{{ old('email', $employee) }}" invalid="{{ $errors->has('email') }}" />

        <x-label for="bu_start" value="BU-Beginn:" />
        <select id="bu_start" name="bu_start" class="form-select">
            @foreach ($bu as $value => $text)
                <option value="{{ $value }}" @selected(old('bu_start', $employee) == $value)>{{ $text }}</option>
            @endforeach
        </select>

        <div class="form-group text-center mt-4">
            <x-button>Speichern</x-button>
            <x-link-button href="{{ route('employees.index') }}" class="btn-secondary">Abbrechen</x-link-button>
        </div>
    </form>
@endsection
