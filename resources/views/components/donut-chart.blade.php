@props([
    'score' => 0,
    'size' => 140,
    'strokeWidth' => 12,
])

@php
    $radius = ($size - $strokeWidth) / 2;
    $circumference = 2 * pi($radius);
    $offset = $circumference - ($score / 100) * $circumference;
    $center = $size / 2;
@endphp

<div class="flex flex-col items-center">
    <p class="text-xs font-semibold uppercase tracking-wider text-text-secondary">Matching Score</p>

    <div class="relative my-2">
        <svg width="{{ $size }}" height="{{ $size }}" class="-rotate-90">
            {{-- Background ring (red) --}}
            <circle
                cx="{{ $center }}"
                cy="{{ $center }}"
                r="{{ $radius }}"
                fill="none"
                stroke="#dc4a3c"
                stroke-width="{{ $strokeWidth }}"
                opacity="0.3"
            />
            {{-- Score ring (teal) --}}
            <circle
                cx="{{ $center }}"
                cy="{{ $center }}"
                r="{{ $radius }}"
                fill="none"
                stroke="#2dd4bf"
                stroke-width="{{ $strokeWidth }}"
                stroke-dasharray="{{ $circumference }}"
                stroke-dashoffset="{{ $offset }}"
                stroke-linecap="round"
                class="transition-all duration-1000 ease-out"
            />
        </svg>

        {{-- Centered score --}}
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="text-2xl font-bold text-white">{{ $score }}%</span>
        </div>
    </div>

    <p class="text-xs text-text-secondary">LLM analysis</p>
</div>
