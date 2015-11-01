@extends('app')

@section('content')
    <h1>Eintrag bearbeiten</h1>

    {!! Form::model($episode, ['method' => 'PUT', 'action' => ['EpisodeController@update', $episode->id]]) !!}

    @include('episodes.form', ['cancel_url' => action('PersonInfoController@showEpisodes', $episode->number)])

    {!! Form::close() !!}
@endsection
