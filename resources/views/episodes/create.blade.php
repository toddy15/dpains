@extends('app')

@section('content')
    {!! Form::open(['action' => 'EpisodeController@store']) !!}

    @include('episodes.form')

    {!! Form::close() !!}
@endsection
