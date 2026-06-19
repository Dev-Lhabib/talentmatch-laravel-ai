@props([
    'status',
])

@php
    $colors = match($status) {
        'convoquer', 'completed' => 'bg-success/20 text-success border border-success/30',
        'attente', 'pending', 'processing', 'analysing' => 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
        'rejeter', 'failed' => 'bg-accent/20 text-accent border border-accent/30',
        default => 'bg-gray-500/20 text-gray-400 border border-gray-500/30',
    };

    $labels = match($status) {
        'convoquer' => 'À convoquer',
        'completed' => 'Complétée',
        'attente' => 'À recontacter',
        'pending' => 'En attente',
        'processing', 'analysing' => 'En cours',
        'rejeter' => 'Rejeté',
        'failed' => 'Échouée',
        default => $status,
    };
@endphp

<span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold {{ $colors }}">
    {{ $labels }}
</span>
