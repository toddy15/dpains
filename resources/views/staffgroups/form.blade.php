<!-- Staffgroup Form Input  -->
<div class="form-group {{ $errors->has('staffgroup') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('staffgroup', 'Mitarbeitergruppe:', ['class' => 'control-label']) !!}
    {!! Form::text('staffgroup', null, ['class' => 'form-control']) !!}
    @if ($errors->has('staffgroup'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<!-- Weight Form Input  -->
<div class="form-group {{ $errors->has('weight') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('weight', 'Reihenfolge:', ['class' => 'control-label']) !!}
    {!! Form::input('number', 'weight', null, ['class' => 'form-control']) !!}
    @if ($errors->has('weight'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<div class="form-group text-center">
    <!-- Speichern Form Input  -->
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
    <!-- Cancel Button -->
    <a class="btn btn-default" href="{{ $cancel_url }}">Abbrechen</a>
</div>
