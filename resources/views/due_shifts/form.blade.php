<x-label for="year" value="Jahr:"/>
<x-input name="year" id="year"
         value="{{ old('year', $due_shift->year ?? '') }}"
         invalid="{{ $errors->has('year') }}"/>

<x-label for="staffgroup_id" value="Mitarbeitergruppe:"/>
{!! Form::select('staffgroup_id', $staffgroups, null, ['class' => 'form-select']) !!}

<x-label for="nights" value="Nächte:"/>
<x-input name="nights" id="nights"
         type="number"
         value="{{ old('nights', $due_shift->nights ?? '') }}"
         invalid="{{ $errors->has('nights') }}"/>

<x-label for="nefs" value="NEF-Schichten:"/>
<x-input name="nefs" id="nefs"
         type="number"
         value="{{ old('nefs', $due_shift->nefs ?? '') }}"
         invalid="{{ $errors->has('nefs') }}"/>

<div class="form-group text-center mt-4">
    <x-button>Speichern</x-button>
    <x-link-button href="{{ route('due_shift.index') }}" class="btn-secondary">Abbrechen</x-link-button>
</div>
