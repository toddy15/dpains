@extends('app')

@section('content')
    {!! Form::model($episode, ['method' => 'PUT', 'action' => ['EpisodeController@update', $episode->id]]) !!}

    @include('episodes.form')

    {!! Form::close() !!}
@endsection
