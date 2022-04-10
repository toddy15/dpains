@extends('layouts.app')

@section('content')
    {!! Form::model($staffgroup, ['method' => 'PUT', 'action' => ['App\Http\Controllers\StaffgroupController@update', $staffgroup->id]]) !!}

    @include('staffgroups.form', [
        'cancel_url' => action('App\Http\Controllers\StaffgroupController@index'),
    ])

    {!! Form::close() !!}
@endsection
