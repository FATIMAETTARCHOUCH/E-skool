@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'bg-gray-50 border border-gray-200 focus:border-indigo-600 focus:ring-0 rounded-lg py-2.5 px-4 text-gray-900 placeholder-gray-400 block w-full transition duration-200']) !!}>