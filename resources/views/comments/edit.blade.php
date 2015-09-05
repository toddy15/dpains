@extends('app')

@section('content')
    {!! Form::model($comment, ['method' => 'PUT', 'action' => ['CommentController@update', $comment->id]]) !!}

    @include('comments.form', ['cancel_url' => action('CommentController@index')])

    {!! Form::close() !!}
@endsection
