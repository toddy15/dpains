@extends('app')

@section('content')
    {!! Form::model($staffgroup, ['method' => 'PUT', 'action' => ['StaffgroupController@update', $staffgroup->id]]) !!}

    @include('staffgroups.form')

    {!! Form::close() !!}
@endsection
