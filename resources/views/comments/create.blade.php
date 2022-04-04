@extends('layouts.app')

@section('content')
    <form action="{{ route('comments.store') }}" method="POST">
        @csrf
        @include('comments.form')

        <div class="form-group text-center">
            {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
            <a class="btn btn-secondary" href="{{ route('comments.index') }}">Abbrechen</a>
        </div>
    </form>
@endsection
