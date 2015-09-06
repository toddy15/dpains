@extends('app')

@section('content')
    {!! Form::model($episode, ['method' => 'PUT', 'action' => ['EpisodeController@update', $episode->id]]) !!}

    @include('episodes.form', ['cancel_url' => action('PersonController@index', $number)])

    {!! Form::close() !!}
@endsection
