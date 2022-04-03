@extends('layouts.app')

@section('content')
    <h1>Neuen Eintrag erstellen</h1>

    {!! Form::model($episode, ['action' => 'App\Http\Controllers\EpisodeController@store']) !!}

    {!! Form::hidden('employee_id', $episode->employee_id) !!}

    @if (!empty($episode->employee_id))
        @include('episodes.form', ['cancel_url' => action('App\Http\Controllers\EmployeeController@showEpisodes', $episode->employee_id)])
    @else
        @include('episodes.form', ['cancel_url' => action('App\Http\Controllers\EmployeeController@index')])
    @endif

    {!! Form::close() !!}
@endsection
