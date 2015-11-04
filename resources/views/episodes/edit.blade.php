@extends('app')

@section('content')
    <h1>Eintrag bearbeiten</h1>

    {!! Form::model($episode, ['method' => 'PUT', 'action' => ['EpisodeController@update', $episode->id]]) !!}

    @include('episodes.form', ['cancel_url' => action('EmployeeController@showEpisodes', $episode->employee_id)])

    {!! Form::close() !!}
@endsection
