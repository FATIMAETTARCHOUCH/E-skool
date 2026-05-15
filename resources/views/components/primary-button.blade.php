<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-8 py-4 bg-brand-600 border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-[0.2em] shadow-glow hover:bg-brand-700 active:scale-95 focus:outline-none transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>