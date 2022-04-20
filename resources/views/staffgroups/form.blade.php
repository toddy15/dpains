<x-label for="staffgroup" value="Mitarbeitergruppe:" />
<x-input value="{{ old('staffgroup', $staffgroup ?? '') }}" name="staffgroup" id="staffgroup" required autofocus
    invalid="{{ $errors->has('staffgroup') }}" />

<x-label for="weight" value="Reihenfolge:" />
<x-input type="number" value="{{ old('weight', $staffgroup ?? '') }}" name="weight" id="weight" required
    invalid="{{ $errors->has('weight') }}" />

<div class="form-group text-center mt-4">
    <x-button>Speichern</x-button>
    <x-link-button href="{{ route('staffgroups.index') }}" class="btn-secondary">Abbrechen</x-link-button>
</div>
