<x-label for="comment" value="Bemerkung:" />
<x-input value="{{ old('comment', $comment ?? '') }}" name="comment" id="comment" required autofocus
    invalid="{{ $errors->has('comment') }}" />

<div class="form-group text-center mt-4">
    <x-button>Speichern</x-button>
    <x-link-button href="{{ route('comments.index') }}" class="btn-secondary">Abbrechen</x-link-button>
</div>
