@csrf

<div class="space-y-4">
    <div>
        <label for="titre" class="mb-1 block text-sm font-medium text-text-secondary">Titre</label>
        <input type="text" id="titre" name="titre" value="{{ old('titre', $offre->titre ?? '') }}" required maxlength="255"
            class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
        @error('titre')
            <p class="mt-1 text-xs text-accent">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-1 block text-sm font-medium text-text-secondary">Description</label>
        <input type="text" id="description" name="description" value="{{ old('description', $offre->description ?? '') }}" required minlength="20"
            class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
        @error('description')
            <p class="mt-1 text-xs text-accent">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="experience_min" class="mb-1 block text-sm font-medium text-text-secondary">Expérience minimum (années)</label>
        <input type="number" id="experience_min" name="experience_min" value="{{ old('experience_min', $offre->experience_min ?? 0) }}" min="0" max="50"
            class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
        @error('experience_min')
            <p class="mt-1 text-xs text-accent">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-text-secondary">Compétences requises</label>
        @error('competences')
            <p class="mb-1 text-xs text-accent">{{ $message }}</p>
        @enderror
        <div class="flex flex-wrap gap-2">
            @foreach($competences as $competence)
                <label class="inline-flex items-center gap-1.5 text-sm text-text-secondary">
                    <input type="checkbox" name="competences[]" value="{{ $competence->nom }}"
                        {{ in_array($competence->id, $selectedCompetenceIds ?? []) ? 'checked' : '' }}
                        class="h-4 w-4 rounded accent-accent">
                    {{ $competence->nom }}
                </label>
            @endforeach
        </div>
        <input type="text" name="competences_new" placeholder="Ajouter une compétence (tapez et appuyez sur Entrée)"
            class="mt-2 w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
    </div>
</div>
