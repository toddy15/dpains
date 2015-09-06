@extends('app')

@section('content')
    {!! Form::open(['action' => 'EpisodeController@store']) !!}

    // @TODO: Update cancel url
    {!! Form::hidden('number', $number) !!}
    @include('episodes.form', ['cancel_url' => action('PersonController@index', $number)])

    {!! Form::close() !!}
@endsection
