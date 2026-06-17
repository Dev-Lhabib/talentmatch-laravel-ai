<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 rounded-lg bg-accent font-semibold text-xs text-white uppercase tracking-widest transition hover:bg-accent/80']) }}>
    {{ $slot }}
</button>
