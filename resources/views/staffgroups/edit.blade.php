@extends('layouts.app')

@section('content')
    <form action="{{ route('staffgroups.update', $staffgroup) }}" method="POST">
        @csrf
        @method('PUT')

        @include('staffgroups.form')
    </form>
@endsection
