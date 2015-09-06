@extends('app')

@section('content')
    {!! Form::open(['action' => 'EpisodeController@store']) !!}

    // @TODO: Update cancel url
    @include('episodes.form', ['cancel_url' => action('PersonController@index', 1)])

    {!! Form::close() !!}
@endsection
