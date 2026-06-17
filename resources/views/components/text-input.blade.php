@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal']) }}>
