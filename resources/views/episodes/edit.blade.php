@extends('layouts.app')

@section('content')
    <h1>Eintrag bearbeiten</h1>

    <form action="{{ route('episodes.update', $episode) }}" method="POST">
        @csrf
        @method('PUT')

        @include('episodes.form')

        <div class="form-group text-center mt-4">
            <x-button>Speichern</x-button>
            <x-link-button
                href="{{ action('App\Http\Controllers\EmployeeController@showEpisodes', $episode->employee_id) }}"
                class="btn-secondary">Abbrechen</x-link-button>
        </div>
    </form>
@endsection
