<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 border border-border rounded-lg font-semibold text-xs text-text-secondary uppercase tracking-widest transition hover:bg-card-hover hover:text-white']) }}>
    {{ $slot }}
</button>
