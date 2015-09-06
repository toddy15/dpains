@extends('app')

@section('content')
    {!! Form::model($episode, ['action' => 'EpisodeController@store']) !!}

    // @TODO: Update cancel url
    {!! Form::hidden('number', $number) !!}
    @include('episodes.form', ['cancel_url' => action('PersonController@show', $number)])

    {!! Form::close() !!}
@endsection
