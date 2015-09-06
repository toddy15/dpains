@extends('app')

@section('content')
    {!! Form::model($episode, ['method' => 'PUT', 'action' => ['EpisodeController@update', $episode->id]]) !!}

    // @TODO: Update cancel url
    @include('episodes.form', ['cancel_url' => action('PersonController@index', 1)])

    {!! Form::close() !!}
@endsection
