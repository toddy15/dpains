@extends('app')

@section('content')
    {!! Form::model($due_shift, ['method' => 'PUT', 'action' => ['DueShiftController@update', $due_shift->id]]) !!}

    @include('due_shifts.form', ['cancel_url' => action('DueShiftController@index')])

    {!! Form::close() !!}
@endsection
