@extends('app')

@section('content')
    {!! Form::open(['action' => 'EpisodeController@store']) !!}

    // @TODO: Update cancel url
    @include('episodes.form', ['cancel_url' => action('CommentController@index')])

    {!! Form::close() !!}
@endsection
