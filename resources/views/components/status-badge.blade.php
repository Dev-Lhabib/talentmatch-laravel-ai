@props([
    'status',
])

@php
    $colors = match($status) {
        'convoquer' => 'bg-success/20 text-success',
        'attente' => 'bg-yellow-500/20 text-yellow-400',
        'rejeter' => 'bg-accent/20 text-accent',
        default => 'bg-gray-500/20 text-gray-400',
    };

    $labels = match($status) {
        'convoquer' => 'À convoquer',
        'attente' => 'En attente',
        'rejeter' => 'Rejeté',
        default => $status,
    };
@endphp

<span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold {{ $colors }}">
    {{ $labels }}
</span>
