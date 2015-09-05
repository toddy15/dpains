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

<!-- Speichern Form Input  -->
<div class="form-group">
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary form-control']) !!}
</div>
