@props(['value'])

{{--<label {{ $attributes->merge(['class' => 'block font-medium text-gray-700']) }}>--}}
<label {{ $attributes->merge(['class' => 'form-label']) }}>
    {{ $value ?? $slot }}
</label>
