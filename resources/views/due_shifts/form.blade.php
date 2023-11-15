<x-label for="year" value="Jahr:" />
<x-input name="year" id="year" type="number" autofocus required value="{{ old('year', $due_shift ?? '') }}"
    invalid="{{ $errors->has('year') }}" />

<x-label for="staffgroup_id" value="Gruppe:" />
<select id="staffgroup_id" name="staffgroup_id" class="form-select" aria-label="Gruppe">
    @foreach ($staffgroups as $staffgroup)
        <option value="{{ $staffgroup->id }}" @selected(old('staffgroup_id', $due_shift ?? '') == $staffgroup->id)>{{ $staffgroup->staffgroup }}</option>
    @endforeach
</select>

<x-label for="nights" value="Nächte:" />
<x-input name="nights" id="nights" type="number" required value="{{ old('nights', $due_shift ?? '') }}"
    invalid="{{ $errors->has('nights') }}" />

<x-label for="nefs" value="NEF-Schichten:" />
<x-input name="nefs" id="nefs" type="number" required value="{{ old('nefs', $due_shift ?? '') }}"
    invalid="{{ $errors->has('nefs') }}" />

<div class="form-group text-center mt-4">
    <x-button>Speichern</x-button>
    <x-link-button href="{{ route('due_shifts.index') }}" class="btn-secondary">Abbrechen</x-link-button>
</div>
