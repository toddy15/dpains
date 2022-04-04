@extends('layouts.app')

@section('content')
    <form action="{{ route('comments.update', $comment) }}" method="POST">
        @csrf
        @method('PUT')

        @include('comments.form')
    </form>
@endsection
