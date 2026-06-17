@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'mt-1 space-y-1 text-xs text-accent']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
