@extends('app')

@section('content')
    <h1>Neuen Eintrag erstellen</h1>

    {!! Form::model($episode, ['action' => 'EpisodeController@store']) !!}

    {!! Form::hidden('number', $episode->number) !!}
    @if (!empty($episode->number))
        @include('episodes.form', ['cancel_url' => action('PersonInfoController@showEpisodes', $episode->number)])
    @else
        @include('episodes.form', ['cancel_url' => action('MonthController@show', [date('Y'), date('m')])])
    @endif

    {!! Form::close() !!}
@endsection
