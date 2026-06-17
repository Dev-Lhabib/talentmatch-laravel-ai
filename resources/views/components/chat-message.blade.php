@props([
    'role' => 'user',
    'content',
    'label' => null,
])

@php
    $isUser = $role === 'user';
    $avatarBg = $isUser ? 'bg-accent' : 'bg-teal';
    $defaultLabel = $isUser ? 'HR Agent' : 'AI';
    $displayLabel = $label ?? $defaultLabel;
@endphp

<div class="flex items-start gap-3">
    {{-- Avatar --}}
    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full {{ $avatarBg }}">
        @if($isUser)
            <span class="text-xs font-bold text-white">{{ substr($displayLabel, 0, 1) }}</span>
        @else
            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>
        @endif
    </div>

    {{-- Message Content --}}
    <div class="min-w-0 flex-1">
        <p class="mb-1 text-xs font-semibold text-white">{{ $displayLabel }}</p>
        <div class="rounded-lg rounded-tl-none bg-card px-4 py-2.5 text-sm leading-relaxed text-text-secondary border border-border">
            {!! nl2br(e($content)) !!}
        </div>
    </div>
</div>
