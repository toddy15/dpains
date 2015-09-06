@extends('app')

@section('content')
    {!! Form::model($episode, ['method' => 'PUT', 'action' => ['EpisodeController@update', $episode->id]]) !!}

    @include('episodes.form', ['cancel_url' => action('PersonController@show', $number)])

    {!! Form::close() !!}
@endsection
