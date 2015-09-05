<!-- Comment Form Input  -->
<div class="form-group">
    {!! Form::label('comment', 'Bemerkung:', ['class' => 'control-label']) !!}
    {!! Form::text('comment', null, ['class' => 'form-control']) !!}
</div>

<!-- Speichern Form Input  -->
<div class="form-group">
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary form-control']) !!}
</div>
