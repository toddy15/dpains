@extends('layouts.app')

@section('content')
    {!! Form::open(['action' => 'App\Http\Controllers\DueShiftController@store']) !!}

    @include('due_shifts.form', ['cancel_url' => action('App\Http\Controllers\DueShiftController@index')])

    {!! Form::close() !!}
@endsection
