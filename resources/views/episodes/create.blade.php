@extends('app')

@section('content')
    {!! Form::model($episode, ['action' => 'EpisodeController@store']) !!}

    // @TODO: Update cancel url
    {!! Form::hidden('number', $episode->number) !!}
    @include('episodes.form', ['cancel_url' => action('PersonController@show', $episode->number)])

    {!! Form::close() !!}
@endsection
