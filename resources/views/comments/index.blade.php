@extends('layouts.app')

@section('content')
    <h1>Bemerkungen</h1>
    <p>
        <a class="btn btn-primary" href="{{ route('comments.create') }}">Neue Bemerkung erstellen</a>
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
                    <td><a class="btn btn-primary" href="{{ route('comments.edit', $comment) }}">Bearbeiten</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
