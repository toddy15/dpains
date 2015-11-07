@extends('app')

@section('content')
    <h1>Einträge für {{ $latest_name }}</h1>
    <table class="table table-striped">
        <thead>
        <th>Name</th>
        <th>Beginn</th>
        <th>Mitarbeitergruppe</th>
        <th>VK</th>
        <th>Faktor Nachtdienst</th>
        <th>Faktor NEF</th>
        <th>Bemerkung</th>
        </thead>
        <tbody>
        @foreach($episodes as $episode)
            <tr>
                <td>{{ $episode->name }}</td>
                <td>{{ $episode->start_date }}</td>
                <td>{{ $episode->staffgroup['staffgroup'] }}</td>
                <td>{{ $episode->vk }}</td>
                <td>{{ $episode->factor_night }}</td>
                <td>{{ $episode->factor_nef }}</td>
                <td>{{ $episode->comment['comment'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
