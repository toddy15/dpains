@extends('layouts.app')

@section('content')
    <form action="{{ route('due_shifts.store') }}" method="POST">
        @csrf

        @include('due_shifts.form')
    </form>
@endsection
