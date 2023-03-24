@extends('layouts.app')

@section('content')
    <h1>Einträge für {{ $latest_name }}</h1>
    <p>
        <a class="btn btn-primary"
            href="{{ action([\App\Http\Controllers\EpisodeController::class, 'create'], ['employee_id' => $employee->id]) }}">Neuen
            Eintrag
            erstellen</a>
    </p>
    <table class="table table-striped">
        <thead>
            <th>Name</th>
            <th>Beginn</th>
            <th>Gruppe</th>
            <th>VK</th>
            <th>Faktor Nachtdienst</th>
            <th>Faktor NEF</th>
            <th>Bemerkung</th>
            <th>Aktion</th>
        </thead>
        <tbody>
            @foreach ($episodes as $episode)
                <tr>
                    <td>{{ $episode->name }}</td>
                    <td>{{ $episode->start_date }}</td>
                    <td>{{ $episode->staffgroup['staffgroup'] }}</td>
                    <td>{{ $episode->vk }}</td>
                    <td>{{ $episode->factor_night }}</td>
                    <td>{{ $episode->factor_nef }}</td>
                    <td>{{ $episode->comment['comment'] ?? '' }}</td>
                    <td>
                        <form action="{{ route('episodes.destroy', $episode) }}" method="post">
                            @csrf
                            @method('DELETE')

                            <x-link-button href="{{ route('episodes.edit', $episode) }}" class="btn-primary">
                                Bearbeiten
                            </x-link-button>
                            <x-button class="btn-danger">Löschen</x-button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
