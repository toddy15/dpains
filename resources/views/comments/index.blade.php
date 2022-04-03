@extends('app')

@section('content')
    <h1>Bemerkungen</h1>
    <p>
        <a class="btn btn-primary" href="{{ action('App\Http\Controllers\CommentController@create') }}">Neue Bemerkung erstellen</a>
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
                    <td><a class="btn btn-primary" href="{{ action('App\Http\Controllers\CommentController@edit', $comment->id) }}">Bearbeiten</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
