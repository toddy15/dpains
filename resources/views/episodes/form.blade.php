<!-- Name Form Input  -->
<div class="form-group">
    {!! Form::label('name', 'Name:', ['class' => 'control-label']) !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Start_date Form Input  -->
<div class="form-group">
    {!! Form::label('start_date', 'Beginnt im Monat:', ['class' => 'control-label']) !!}
    {!! Form::text('start_date', null, ['class' => 'form-control']) !!}
</div>

<!-- Staffgroup Form Input  -->
<div class="form-group">
    {!! Form::label('staffgroup_id', 'Mitarbeitergruppe:', ['class' => 'control-label']) !!}
    {!! Form::select('staffgroup_id', $staffgroups, null, ['class' => 'form-control']) !!}
</div>

<!-- Vk Form Input  -->
<div class="form-group">
    {!! Form::label('vk', 'VK:', ['class' => 'control-label']) !!}
    {!! Form::text('vk', null, ['class' => 'form-control']) !!}
</div>

<!-- Factor_night Form Input  -->
<div class="form-group">
    {!! Form::label('factor_night', 'Faktor für Nachtdienste:', ['class' => 'control-label']) !!}
    {!! Form::text('factor_night', null, ['class' => 'form-control']) !!}
</div>

<!-- Factor_nef Form Input  -->
<div class="form-group">
    {!! Form::label('factor_nef', 'Faktor für NEF-Dienste:', ['class' => 'control-label']) !!}
    {!! Form::text('factor_nef', null, ['class' => 'form-control']) !!}
</div>

<!-- Comment Form Input  -->
<div class="form-group">
    {!! Form::label('comment_id', 'Bemerkung:', ['class' => 'control-label']) !!}
    {!! Form::select('comment_id', $comments, null, ['class' => 'form-control']) !!}
</div>

<div class="form-group text-center">
    <!-- Speichern Form Input  -->
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
    <!-- Cancel Button -->
    <a class="btn btn-danger" href="{{ $cancel_url }}">Abbrechen</a>
</div>
