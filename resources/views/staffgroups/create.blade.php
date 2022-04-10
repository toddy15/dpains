@extends('layouts.app')

@section('content')
    {!! Form::open(['action' => 'App\Http\Controllers\StaffgroupController@store']) !!}

    @include('staffgroups.form', [
        'cancel_url' => action('App\Http\Controllers\StaffgroupController@index'),
    ])

    {!! Form::close() !!}
@endsection
