@csrf

<div class="space-y-4">
    <div>
        <label for="title" class="mb-1 block text-sm font-medium text-text-secondary">Titre</label>
        <input type="text" id="title" name="title" value="{{ old('title', $offer->title ?? '') }}" required maxlength="255"
            class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
        @error('title')
            <p class="mt-1 text-xs text-accent">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-1 block text-sm font-medium text-text-secondary">Description</label>
        <textarea id="description" name="description" rows="6" required minlength="20" maxlength="10000"
            class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal"
            placeholder="Décrivez le poste...">{{ old('description', $offer->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-accent">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="experience_min" class="mb-1 block text-sm font-medium text-text-secondary">Expérience min. (années)</label>
            <input type="number" id="experience_min" name="experience_min" value="{{ old('experience_min', $offer->experience_min ?? '') }}" min="0" max="50"
                class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
            @error('experience_min')
                <p class="mt-1 text-xs text-accent">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="status" class="mb-1 block text-sm font-medium text-text-secondary">Statut</label>
            <select id="status" name="status"
                class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
                <option value="open" {{ old('status', $offer->status ?? 'open') === 'open' ? 'selected' : '' }}>Ouvert</option>
                <option value="closed" {{ old('status', $offer->status ?? 'open') === 'closed' ? 'selected' : '' }}>Fermé</option>
                <option value="draft" {{ old('status', $offer->status ?? 'open') === 'draft' ? 'selected' : '' }}>Brouillon</option>
            </select>
            @error('status')
                <p class="mt-1 text-xs text-accent">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div x-data="{
        skills: @js(old('required_skills', $offer->required_skills ?? [])),
        newSkill: '',
        addSkill() {
            const s = this.newSkill.trim();
            if (s && !this.skills.includes(s)) { this.skills.push(s); }
            this.newSkill = '';
        },
        removeSkill(idx) { this.skills.splice(idx, 1); }
    }">
        <label class="mb-1 block text-sm font-medium text-text-secondary">Compétences requises</label>
        <div class="flex gap-2">
            <input type="text" x-model="newSkill" @keydown.enter.prevent="addSkill()" placeholder="Ajouter une compétence..."
                maxlength="100"
                class="flex-1 rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
            <button type="button" @click="addSkill()" class="rounded-lg bg-teal px-4 py-2.5 text-sm font-medium text-white transition hover:bg-teal/80">
                +
            </button>
        </div>
        <template x-for="(skill, idx) in skills" :key="idx">
            <div class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-bg px-3 py-1 text-xs text-text-secondary border border-border">
                <span x-text="skill"></span>
                <input type="hidden" :name="'required_skills[' + idx + ']'" :value="skill">
                <button type="button" @click="removeSkill(idx)" class="text-accent hover:text-accent/80">&times;</button>
            </div>
        </template>
        @error('required_skills')
            <p class="mt-1 text-xs text-accent">{{ $message }}</p>
        @enderror
    </div>
</div>
