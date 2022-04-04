@extends('layouts.app')

@section('content')
    <h1>Bemerkungen</h1>
    <p>
        <x-link-button class="btn-primary" href="{{ route('comments.create') }}">Neue Bemerkung erstellen</x-link-button>
    </p>
    <table class="table table-striped">
        <thead>
            <th>Bemerkung</th>
            <th>Aktion</th>
        </thead>
        <tbody>
            @foreach($comments as $comment)
                <tr>
                    <td>{{ $comment->comment }}</td>
                    <td><x-link-button class="btn-primary" href="{{ route('comments.edit', $comment) }}">Bearbeiten</x-link-button></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
