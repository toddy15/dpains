@extends('layouts.app')

@section('content')
    <h1>Neuen Eintrag erstellen</h1>

    <form action="{{ route('episodes.store') }}" method="POST">
        @csrf
        <input type="hidden" name="employee_id" value="{{ $episode->employee_id }}">

        @include('episodes.form')

        <div class="form-group text-center mt-4">
            <x-button>Speichern</x-button>
            @if (!empty($episode->employee_id))
                <x-link-button href="{{ route('employees.episodes.index', $episode->employee_id) }}"
                    class="btn-secondary">Abbrechen</x-link-button>
            @else
                <x-link-button href="{{ route('employees.index') }}" class="btn-secondary">Abbrechen</x-link-button>
            @endif
        </div>
    </form>
@endsection
