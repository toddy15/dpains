@extends('layouts.app')

@section('content')
    {!! Form::model($comment, ['method' => 'PUT', 'action' => ['App\Http\Controllers\CommentController@update', $comment->id]]) !!}

    @include('comments.form', ['cancel_url' => action('App\Http\Controllers\CommentController@index')])

    {!! Form::close() !!}
@endsection
