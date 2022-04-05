{{--<textarea {!! $attributes->merge(['class' => 'rounded-md shadow-sm border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50']) !!}>{{ $slot }}</textarea>--}}
<textarea {!! $attributes->merge(['class' => 'form-control']) !!}>{{ $slot }}</textarea>
