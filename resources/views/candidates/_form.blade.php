@csrf

<div class="space-y-4">
    <div>
        <label for="name" class="mb-1 block text-sm font-medium text-text-secondary">Nom</label>
        <input type="text" id="name" name="name" value="{{ old('name', $candidate->name ?? '') }}" required maxlength="255"
            class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
        @error('name')
            <p class="mt-1 text-xs text-accent">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="mb-1 block text-sm font-medium text-text-secondary">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $candidate->email ?? '') }}" maxlength="255"
            class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
        @error('email')
            <p class="mt-1 text-xs text-accent">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="phone" class="mb-1 block text-sm font-medium text-text-secondary">Téléphone</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone', $candidate->phone ?? '') }}" maxlength="50"
            class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
        @error('phone')
            <p class="mt-1 text-xs text-accent">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="cv_text" class="mb-1 block text-sm font-medium text-text-secondary">CV (texte brut, minimum 50 caractères)</label>
        <textarea id="cv_text" name="cv_text" rows="10" required minlength="50" maxlength="50000"
            class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal"
            placeholder="Collez le CV du candidat ici...">{{ old('cv_text', $candidate->cv_text ?? '') }}</textarea>
        @error('cv_text')
            <p class="mt-1 text-xs text-accent">{{ $message }}</p>
        @enderror
    </div>
</div>
