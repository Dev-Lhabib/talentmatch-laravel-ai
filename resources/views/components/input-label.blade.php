@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-1 block text-sm font-medium text-text-secondary']) }}>
    {{ $value ?? $slot }}
</label>
