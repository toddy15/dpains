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

<!-- Speichern Form Input  -->
<div class="form-group">
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary form-control']) !!}
</div>
