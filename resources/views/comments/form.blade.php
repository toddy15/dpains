<x-label for="comment" value="Bemerkung:"/>
<x-input
    value="{{ old('comment', $comment->comment ?? '') }}"
    name="comment" id="comment" required autofocus invalid="{{ $errors->has('comment') }}"/>
