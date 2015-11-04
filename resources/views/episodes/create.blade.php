@extends('app')

@section('content')
    <h1>Neuen Eintrag erstellen</h1>

    {!! Form::model($episode, ['action' => 'EpisodeController@store']) !!}

    {!! Form::hidden('employee_id', $episode->employee_id) !!}

    @if (!empty($episode->employee_id))
        @include('episodes.form', ['cancel_url' => action('EmployeeController@showEpisodes', $episode->employee_id)])
    @else
        @include('episodes.form', ['cancel_url' => action('MonthController@show', [date('Y'), date('m')])])
    @endif

    {!! Form::close() !!}
@endsection
