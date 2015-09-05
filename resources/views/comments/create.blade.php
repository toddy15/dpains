@extends('app')

@section('content')
    {!! Form::open(['action' => 'CommentController@store']) !!}

    @include('comments.form', ['cancel_url' => action('CommentController@index')])

    {!! Form::close() !!}
@endsection
