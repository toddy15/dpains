@extends('app')

@section('content')
    {!! Form::open(['action' => 'App\Http\Controllers\CommentController@store']) !!}

    @include('comments.form', ['cancel_url' => action('App\Http\Controllers\CommentController@index')])

    {!! Form::close() !!}
@endsection
