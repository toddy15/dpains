@extends('app')

@section('content')
    {!! Form::open(['action' => 'StaffgroupController@store']) !!}

    @include('staffgroups.form')

    {!! Form::close() !!}
@endsection
