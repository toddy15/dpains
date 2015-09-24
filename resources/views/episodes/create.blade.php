@extends('app')

@section('content')
    {!! Form::model($episode, ['action' => 'EpisodeController@store']) !!}

    @if (!empty($episode->number))
        {!! Form::hidden('number', $episode->number) !!}
        @include('episodes.form', ['cancel_url' => action('PersonController@show', $episode->number)])
    @else
        @include('episodes.form', ['cancel_url' => action('MonthController@show', [date('Y'), date('m')])])
    @endif

    {!! Form::close() !!}
@endsection
