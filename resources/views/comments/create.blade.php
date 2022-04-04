@extends('layouts.app')

@section('content')
    <form action="{{ route('comments.store') }}" method="POST">
        @csrf

        @include('comments.form')
    </form>
@endsection
