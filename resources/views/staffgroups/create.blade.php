@extends('layouts.app')

@section('content')
    <form action="{{ route('staffgroups.store') }}" method="POST">
        @csrf

        @include('staffgroups.form')
    </form>
@endsection
