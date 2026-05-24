<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-black text-xs text-white uppercase tracking-[0.1em] hover:bg-indigo-700 active:scale-95 focus:outline-none transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>