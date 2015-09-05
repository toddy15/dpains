<!-- Comment Form Input  -->
<div class="form-group">
    {!! Form::label('comment', 'Bemerkung:', ['class' => 'control-label']) !!}
    {!! Form::text('comment', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group text-center">
    <!-- Speichern Form Input  -->
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
    <!-- Cancel Button -->
    <a class="btn btn-danger" href="{{ $cancel_url }}">Abbrechen</a>
</div>
