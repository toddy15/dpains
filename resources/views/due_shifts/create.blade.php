@extends('app')

@section('content')
    {!! Form::open(['action' => 'DueShiftController@store']) !!}

    @include('due_shifts.form', ['cancel_url' => action('DueShiftController@index')])

    {!! Form::close() !!}
@endsection
