@extends('app')

@section('content')
    <h1>Backups</h1>
    <a class="btn btn-primary" href="{{ action('BackupController@download') }}">Backup herunterladen</a>

    <h2>Backup hochladen</h2>
    {!! Form::open(['action' => 'BackupController@restore', 'enctype' => 'multipart/form-data']) !!}

    <!-- Backup Form Input  -->
    <div class="form-group">
        {!! Form::label('backup', 'Backup-Datei:', ['class' => 'control-label']) !!}
        {!! Form::file('backup') !!}
    </div>

    <div class="form-group">
        <!-- Speichern Form Input  -->
        {!! Form::submit('Hochladen', ['class' => 'btn btn-primary']) !!}
                <!-- Cancel Button -->
        <a class="btn btn-default" href="{{ action('BackupController@index') }}">Abbrechen</a>
    </div>

    {!! Form::close() !!}
@endsection
