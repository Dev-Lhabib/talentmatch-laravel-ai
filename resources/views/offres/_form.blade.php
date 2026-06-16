@csrf

<div class="form-group">
    <label for="titre">Titre</label>
    <input type="text" id="titre" name="titre" value="{{ old('titre', $offre->titre ?? '') }}" required maxlength="255">
    @error('titre')
        <div class="errors">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="description">Description</label>
    <input type="text" id="description" name="description" value="{{ old('description', $offre->description ?? '') }}" required minlength="20">
    @error('description')
        <div class="errors">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="experience_min">Expérience minimum (années)</label>
    <input type="number" id="experience_min" name="experience_min" value="{{ old('experience_min', $offre->experience_min ?? 0) }}" min="0" max="50">
    @error('experience_min')
        <div class="errors">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label>Compétences requises</label>
    @error('competences')
        <div class="errors">{{ $message }}</div>
    @enderror
    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.25rem;">
        @foreach($competences as $competence)
            <label style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.875rem; font-weight: 400;">
                <input type="checkbox" name="competences[]" value="{{ $competence->nom }}"
                    {{ in_array($competence->id, $selectedCompetenceIds ?? []) ? 'checked' : '' }}>
                {{ $competence->nom }}
            </label>
        @endforeach
    </div>
    <input type="text" name="competences_new" placeholder="Ajouter une compétence (tapez et appuyez sur Entrée)" style="margin-top: 0.5rem;">
</div>
