@extends('app')

@section('content')
    {!! Form::open(['action' => 'CommentController@store']) !!}

    @include('comments.form')

    {!! Form::close() !!}
@endsection
