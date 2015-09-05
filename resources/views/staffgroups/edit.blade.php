@extends('app')

@section('content')
    {!! Form::model($staffgroup, ['method' => 'PUT', 'action' => ['StaffgroupController@update', $staffgroup->id]]) !!}

    @include('staffgroups.form', ['cancel_url' => action('StaffgroupController@index')])

    {!! Form::close() !!}
@endsection
