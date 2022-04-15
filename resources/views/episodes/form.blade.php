<!-- Name Form Input  -->
<div class="form-group {{ $errors->has('name') ? 'has-error has-feedback' : '' }}">
    {!! Form::label('name', 'Name:', ['class' => 'form-label']) !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
    @if ($errors->has('name'))
        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
    @endif
</div>

<x-label for="month" value="Beginnt im Monat:" class="col-form-label" />
<select id="month" name="month" class="form-select" aria-label="Monat">
    @foreach ($month_names as $number => $month_name)
        <option value="{{ $number }}" @selected(old('month', $episode->month) == $number)>{{ $month_name }}</option>
    @endforeach
</select>

<x-label for="year" value="Jahr:" class="col-form-label" />
<select id="year" name="year" class="form-select" aria-label="Jahr">
    @for ($y = $start_year; $y <= $end_year; $y++)
        <option @selected(old('year', $episode->year) == $y)>{{ $y }}</option>
    @endfor
</select>

<x-label for="staffgroup_id" value="Mitarbeitergruppe:" />
<select id="staffgroup_id" name="staffgroup_id" class="form-select" aria-label="Mitarbeitergruppe">
    @foreach ($staffgroups as $staffgroup)
        <option value="{{ $staffgroup->id }}" @selected(old('staffgroup_id', $episode->staffgroup_id) == $staffgroup->id)>{{ $staffgroup->staffgroup }}</option>
    @endforeach
</select>

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

<x-label for="comment_id" value="Bemerkung:" />
<select id="comment_id" name="comment_id" class="form-select" aria-label="Bemerkung">
    <option value=""></option>
    @foreach ($comments as $comment)
        <option value="{{ $comment->id }}" @selected(old('comment_id', $episode->comment_id) == $comment->id)>{{ $comment->comment }}</option>
    @endforeach
</select>

<div class="form-group text-center mt-4">
    <!-- Speichern Form Input  -->
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
    <!-- Cancel Button -->
    <a class="btn btn-secondary" href="{{ $cancel_url }}">Abbrechen</a>
</div>
