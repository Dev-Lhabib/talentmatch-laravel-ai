@extends("layouts.app")

@section("content")
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-white">Retour d experience</h1>
        <p class="mt-1 text-sm text-text-secondary">Partagez vos suggestions ou signalez un probleme.</p>
    </div>

    @if(session("success"))
        <div class="mb-4 rounded-lg bg-teal/10 p-3 text-sm text-teal">
            {{ session("success") }}
        </div>
    @endif

    <div class="rounded-xl border border-border bg-card p-6"
         x-data="{
            type: &apos;&apos;,
            offreId: &apos;&apos;,
            candidateId: &apos;&apos;,
            priorite: &apos;medium&apos;,
         }">
        <form method="POST" action="{{ route("feedback.store") }}" class="space-y-6">
            @csrf

            {{-- Type de retour --}}
            <div>
                <label class="mb-3 block text-sm font-semibold text-white">Type de retour <span class="text-accent">*</span></label>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <label @click="type = &apos;bug&apos;"
                           class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition"
                           :class="type === &apos;bug&apos; ? &apos;border-accent bg-accent/10&apos; : &apos;border-border bg-card hover:border-accent/50&apos;">
                        <span class="text-xl">🐛</span>
                        <div>
                            <p class="text-sm font-medium text-white">Bug / Problème technique</p>
                            <p class="text-xs text-text-secondary">Signaler un dysfonctionnement</p>
                        </div>
                        <input type="radio" name="type" value="bug" class="sr-only" x-model="type">
                    </label>

                    <label @click="type = &apos;suggestion&apos;"
                           class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition"
                           :class="type === &apos;suggestion&apos; ? &apos;border-accent bg-accent/10&apos; : &apos;border-border bg-card hover:border-accent/50&apos;">
                        <span class="text-xl">💡</span>
                        <div>
                            <p class="text-sm font-medium text-white">Suggestion d amelioration</p>
                            <p class="text-xs text-text-secondary">Proposer une idee</p>
                        </div>
                        <input type="radio" name="type" value="suggestion" class="sr-only" x-model="type">
                    </label>

                    <label @click="type = &apos;analyse&apos;"
                           class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition"
                           :class="type === &apos;analyse&apos; ? &apos;border-accent bg-accent/10&apos; : &apos;border-border bg-card hover:border-accent/50&apos;">
                        <span class="text-xl">⭐</span>
                        <div>
                            <p class="text-sm font-medium text-white">Retour sur une analyse IA</p>
                            <p class="text-xs text-text-secondary">Evaluer la pertinence d une analyse</p>
                        </div>
                        <input type="radio" name="type" value="analyse" class="sr-only" x-model="type">
                    </label>

                    <label @click="type = &apos;autre&apos;"
                           class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition"
                           :class="type === &apos;autre&apos; ? &apos;border-accent bg-accent/10&apos; : &apos;border-border bg-card hover:border-accent/50&apos;">
                        <span class="text-xl">🎯</span>
                        <div>
                            <p class="text-sm font-medium text-white">Autre</p>
                            <p class="text-xs text-text-secondary">Tout autre sujet</p>
                        </div>
                        <input type="radio" name="type" value="autre" class="sr-only" x-model="type">
                    </label>
                </div>
                @error("type")
                    <p class="mt-1 text-xs text-accent">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contexte --}}
            <div x-show="type === &apos;analyse&apos;" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-4 rounded-xl border border-teal/20 bg-teal/5 p-4">
                <p class="text-xs font-medium text-teal">Contexte de l analyse</p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="offre_id" class="mb-1 block text-sm font-medium text-text-secondary">Offre concernee</label>
                        <select name="offre_id" id="offre_id" x-model="offreId"
                            class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
                            <option value="" class="bg-card text-text-secondary">Selectionner une offre...</option>
                            @foreach($offres as $offre)
                                <option value="{{ $offre->id }}" class="bg-card text-white">{{ $offre->titre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="candidate_id" class="mb-1 block text-sm font-medium text-text-secondary">Candidat concerne</label>
                        <select name="candidate_id" id="candidate_id" x-model="candidateId"
                            class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
                            <option value="" class="bg-card text-text-secondary">Selectionner un candidat...</option>
                            @foreach($candidates as $candidate)
                                <option value="{{ $candidate->id }}" class="bg-card text-white">{{ $candidate->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Sujet --}}
            <div>
                <label for="sujet" class="mb-1 block text-sm font-medium text-text-secondary">Sujet <span class="text-accent">*</span></label>
                <input type="text" id="sujet" name="sujet" value="{{ old("sujet") }}" required maxlength="255"
                    placeholder="Ex: Amelioration du dashboard, bug detecte..."
                    class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">
                @error("sujet")
                    <p class="mt-1 text-xs text-accent">{{ $message }}</p>
                @enderror
            </div>

            {{-- Message --}}
            <div>
                <label for="message" class="mb-1 block text-sm font-medium text-text-secondary">Message <span class="text-accent">*</span></label>
                <textarea id="message" name="message" rows="6" required minlength="20" maxlength="5000"
                    placeholder="Decrivez votre retour en detail (mini 20 caracteres)..."
                    class="w-full rounded-lg border border-teal-dark bg-transparent px-4 py-2.5 text-sm text-white placeholder-text-secondary outline-none transition focus:border-teal focus:ring-1 focus:ring-teal">{{ old("message") }}</textarea>
                @error("message")
                    <p class="mt-1 text-xs text-accent">{{ $message }}</p>
                @enderror
            </div>

            {{-- Priorite --}}
            <div>
                <label class="mb-3 block text-sm font-medium text-text-secondary">Priorite</label>
                <div class="flex gap-2">
                    <button type="button" @click="priorite = &apos;low&apos;"
                            class="rounded-lg border px-4 py-2 text-sm transition"
                            :class="priorite === &apos;low&apos; ? &apos;border-green-500 bg-green-500/20 text-green-400&apos; : &apos;border-border text-text-secondary hover:border-green-500/50&apos;">
                        Low
                    </button>
                    <button type="button" @click="priorite = &apos;medium&apos;"
                            class="rounded-lg border px-4 py-2 text-sm transition"
                            :class="priorite === &apos;medium&apos; ? &apos;border-yellow-500 bg-yellow-500/20 text-yellow-400&apos; : &apos;border-border text-text-secondary hover:border-yellow-500/50&apos;">
                        Medium
                    </button>
                    <button type="button" @click="priorite = &apos;high&apos;"
                            class="rounded-lg border px-4 py-2 text-sm transition"
                            :class="priorite === &apos;high&apos; ? &apos;border-accent bg-accent/20 text-accent&apos; : &apos;border-border text-text-secondary hover:border-accent/50&apos;">
                        High
                    </button>
                </div>
                <input type="hidden" name="priorite" :value="priorite">
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3 border-t border-border pt-5">
                <a href="{{ route("dashboard") }}" class="text-sm text-text-secondary transition hover:text-white">Annuler</a>
                <button type="submit" class="rounded-lg bg-accent px-6 py-2.5 text-sm font-medium text-white transition hover:bg-accent/80">
                    Envoyer le retour
                </button>
            </div>
        </form>
    </div>
@endsection