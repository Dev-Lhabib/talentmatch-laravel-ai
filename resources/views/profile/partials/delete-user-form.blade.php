<section class="space-y-4">
    <header class="mb-4">
        <h2 class="text-lg font-semibold text-white">Supprimer le compte</h2>
        <p class="mt-1 text-sm text-text-secondary">
            Une fois votre compte supprimé, toutes ses données seront définitivement effacées.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Supprimer le compte</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-white">
                Êtes-vous sûr de vouloir supprimer votre compte ?
            </h2>

            <p class="mt-2 text-sm text-text-secondary">
                Veuillez entrer votre mot de passe pour confirmer la suppression définitive de votre compte.
            </p>

            <div class="mt-4">
                <x-input-label for="password" value="Mot de passe" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Mot de passe"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Annuler
                </x-secondary-button>

                <x-danger-button>
                    Supprimer le compte
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
