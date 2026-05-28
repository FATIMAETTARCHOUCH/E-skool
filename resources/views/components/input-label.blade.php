@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-xs uppercase tracking-widest text-gray-600 dark:text-slate-400 mb-2']) }}>
    {{ $value ?? $slot }}
</label>
