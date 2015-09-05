@extends('app')

@section('content')
    {!! Form::model($comment, ['method' => 'PUT', 'action' => ['CommentController@update', $comment->id]]) !!}

    @include('comments.form')

    {!! Form::close() !!}
@endsection
