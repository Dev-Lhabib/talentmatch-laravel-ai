@props([
    'candidature',
])

<div class="flex items-start gap-4">
    {{-- Avatar --}}
    <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-accent text-lg font-bold text-white">
        {{ substr($candidature->nom_candidat, 0, 1) }}
    </div>

    {{-- Name + Bio --}}
    <div class="min-w-0 flex-1">
        <h3 class="text-lg font-bold text-white">{{ $candidature->nom_candidat }}</h3>
        <p class="mt-1 line-clamp-2 text-sm text-text-secondary">
            {{ Str::limit(strip_tags($candidature->cv_text), 120) }}
        </p>
    </div>
</div>
