@extends('layouts.app')

@section('content')
    {!! Form::model($due_shift, ['method' => 'PUT', 'action' => ['App\Http\Controllers\DueShiftController@update', $due_shift->id]]) !!}

    @include('due_shifts.form', ['cancel_url' => action('App\Http\Controllers\DueShiftController@index')])

    {!! Form::close() !!}
@endsection
