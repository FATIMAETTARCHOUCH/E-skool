@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'bg-gray-50 border border-gray-200 focus:border-indigo-600 focus:ring-0 rounded-lg py-2.5 px-4 text-gray-900 placeholder-gray-400 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:border-indigo-500 block w-full transition duration-200']) !!}>