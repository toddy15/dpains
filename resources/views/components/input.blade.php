@props(['invalid' => false])

@php
$class = $invalid ? ' is-invalid' : '';
@endphp

{{--<input {!! $attributes->merge(['class' => 'rounded-md shadow-sm border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50']) !!}>--}}
<input {!! $attributes->merge(['class' => 'form-control' . $class]) !!}>
