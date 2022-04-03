<!-- Year Form Input  -->
<div class="form-group {{ $errors->has('year') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('year', 'Jahr:', ['class' => 'form-label']) !!}
    {!! Form::text('year', null, ['class' => 'form-control']) !!}
    @if ($errors->has('year'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<!-- Staffgroup Form Input  -->
<div class="form-group {{ $errors->has('staffgroup_id') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('staffgroup_id', 'Mitarbeitergruppe:', ['class' => 'form-label']) !!}
    {!! Form::select('staffgroup_id', $staffgroups, null, ['class' => 'form-control']) !!}
    @if ($errors->has('staffgroup_id'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<!-- Nights Form Input  -->
<div class="form-group {{ $errors->has('nights') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('nights', 'Nächte:', ['class' => 'form-label']) !!}
    {!! Form::text('nights', null, ['class' => 'form-control']) !!}
    @if ($errors->has('nights'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<!-- Nefs Form Input  -->
<div class="form-group {{ $errors->has('nefs') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('nefs', 'NEF-Schichten:', ['class' => 'form-label']) !!}
    {!! Form::text('nefs', null, ['class' => 'form-control']) !!}
    @if ($errors->has('nefs'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<div class="form-group text-center">
    <!-- Speichern Form Input  -->
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
    <!-- Cancel Button -->
    <a class="btn btn-secondary" href="{{ $cancel_url }}">Abbrechen</a>
</div>
