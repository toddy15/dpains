<x-label for="name" value="Name:" />
<x-input value="{{ old('name', $episode ?? '') }}" name="name" id="name" required autofocus
    invalid="{{ $errors->has('name') }}" />

<x-label for="month" value="Beginnt im Monat:" class="col-form-label" />
<select id="month" name="month" class="form-select" aria-label="Monat">
    @foreach ($month_names as $number => $month_name)
        <option value="{{ $number }}" @selected(old('month', $episode) == $number)>{{ $month_name }}</option>
    @endforeach
</select>

<x-label for="year" value="Jahr:" class="col-form-label" />
<select id="year" name="year" class="form-select" aria-label="Jahr">
    @for ($y = $start_year; $y <= $end_year; $y++)
        <option @selected(old('year', $episode) == $y)>{{ $y }}</option>
    @endfor
</select>

<x-label for="staffgroup_id" value="Gruppe:" />
<select id="staffgroup_id" name="staffgroup_id" class="form-select" aria-label="Gruppe">
    @foreach ($staffgroups as $staffgroup)
        <option value="{{ $staffgroup->id }}" @selected(old('staffgroup_id', $episode) == $staffgroup->id)>{{ $staffgroup->staffgroup }}</option>
    @endforeach
</select>

<x-label for="vk" value="VK:" />
<x-input value="{{ old('vk', $episode ?? '') }}" name="vk" id="vk" required invalid="{{ $errors->has('vk') }}" />

<x-label for="factor_night" value="Faktor für Nachtdienste:" />
<x-input value="{{ old('factor_night', $episode ?? '') }}" name="factor_night" id="factor_night" required
    invalid="{{ $errors->has('factor_night') }}" />

<x-label for="factor_nef" value="Faktor für NEF-Dienste:" />
<x-input value="{{ old('factor_nef', $episode ?? '') }}" name="factor_nef" id="factor_nef" required
    invalid="{{ $errors->has('factor_nef') }}" />

<x-label for="comment_id" value="Bemerkung:" />
<select id="comment_id" name="comment_id" class="form-select" aria-label="Bemerkung">
    <option value=""></option>
    @foreach ($comments as $comment)
        <option value="{{ $comment->id }}" @selected(old('comment_id', $episode) == $comment->id)>{{ $comment->comment }}</option>
    @endforeach
</select>
