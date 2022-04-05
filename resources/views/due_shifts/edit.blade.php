@extends('layouts.app')

@section('content')
    <form action="{{ route('due_shifts.update', $due_shift) }}" method="POST">
        @csrf
        @method('PUT')

        @include('due_shifts.form')
    </form>
@endsection
