<!-- Comment Form Input  -->
<div class="form-group {{ $errors->has('comment') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('comment', 'Bemerkung:', ['class' => 'form-label']) !!}
    {!! Form::text('comment', null, ['class' => 'form-control']) !!}
    @if ($errors->has('comment'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<div class="form-group text-center">
    <!-- Speichern Form Input  -->
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
    <!-- Cancel Button -->
    <a class="btn btn-secondary" href="{{ $cancel_url }}">Abbrechen</a>
</div>
