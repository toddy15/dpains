<!-- Name Form Input  -->
<div class="form-group {{ $errors->has('name') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('name', 'Name:', ['class' => 'form-label']) !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
    @if ($errors->has('name'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<!-- Month Form Input  -->
<div class="form-group">
    {!! Form::label('month', 'Beginnt im Monat:', ['class' => 'form-label']) !!}
    <div class="form-inline">
        {!! Form::selectMonth('month', null, ['class' => 'form-control']) !!}
        {!! Form::label('year', 'Jahr:', ['class' => 'sr-only form-label']) !!}
        {!! Form::selectYear('year', $start_year, $end_year, null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Staffgroup Form Input  -->
<div class="form-group {{ $errors->has('staffgroup_id') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('staffgroup_id', 'Mitarbeitergruppe:', ['class' => 'form-label']) !!}
    {!! Form::select('staffgroup_id', $staffgroups, null, ['class' => 'form-control']) !!}
    @if ($errors->has('staffgroup_id'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<!-- Vk Form Input  -->
<div class="form-group {{ $errors->has('vk') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('vk', 'VK:', ['class' => 'form-label']) !!}
    {!! Form::text('vk', null, ['class' => 'form-control']) !!}
    @if ($errors->has('vk'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<!-- Factor_night Form Input  -->
<div class="form-group {{ $errors->has('factor_night') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('factor_night', 'Faktor für Nachtdienste:', ['class' => 'form-label']) !!}
    {!! Form::text('factor_night', null, ['class' => 'form-control']) !!}
    @if ($errors->has('factor_night'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<!-- Factor_nef Form Input  -->
<div class="form-group {{ $errors->has('factor_nef') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('factor_nef', 'Faktor für NEF-Dienste:', ['class' => 'form-label']) !!}
    {!! Form::text('factor_nef', null, ['class' => 'form-control']) !!}
    @if ($errors->has('factor_nef'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<!-- Comment Form Input  -->
<div class="form-group {{ $errors->has('comment_id') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('comment_id', 'Bemerkung:', ['class' => 'form-label']) !!}
    {!! Form::select('comment_id', $comments, null, ['class' => 'form-control']) !!}
    @if ($errors->has('comment_id'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<div class="form-group text-center">
    <!-- Speichern Form Input  -->
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
    <!-- Cancel Button -->
    <a class="btn btn-default" href="{{ $cancel_url }}">Abbrechen</a>
</div>
