<!-- Staffgroup Form Input  -->
<div class="form-group">
    {!! Form::label('staffgroup', 'Mitarbeitergruppe:', ['class' => 'control-label']) !!}
    {!! Form::text('staffgroup', null, ['class' => 'form-control']) !!}
</div>

<!-- Weight Form Input  -->
<div class="form-group">
    {!! Form::label('weight', 'Reihenfolge:', ['class' => 'control-label']) !!}
    {!! Form::input('number', 'weight', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group text-center">
    <!-- Speichern Form Input  -->
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
    <!-- Cancel Button -->
    <a class="btn btn-danger" href="{{ $cancel_url }}">Abbrechen</a>
</div>
