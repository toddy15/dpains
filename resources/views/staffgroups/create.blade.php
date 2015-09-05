@extends('app')

@section('content')
    {!! Form::open(['action' => 'StaffgroupController@store']) !!}

    @include('staffgroups.form', ['cancel_url' => action('StaffgroupController@index')])

    {!! Form::close() !!}
@endsection
