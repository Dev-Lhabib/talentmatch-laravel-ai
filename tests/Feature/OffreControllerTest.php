<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Competence;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OffreControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_index_requires_authentication(): void
    {
        $this->get(route('offres.index'))->assertRedirect('/login');
    }

    public function test_index_displays_user_offres(): void
    {
        $offre = Offre::factory()->for($this->user)->create(['titre' => 'Développeur PHP']);

        $this->actingAs($this->user)
            ->get(route('offres.index'))
            ->assertOk()
            ->assertSee('Développeur PHP');
    }

    public function test_index_does_not_display_other_users_offres(): void
    {
        $otherUser = User::factory()->create();
        Offre::factory()->for($otherUser)->create(['titre' => 'Offre privée']);

        $this->actingAs($this->user)
            ->get(route('offres.index'))
            ->assertDontSee('Offre privée');
    }

    public function test_index_displays_candidate_count(): void
    {
        $offre = Offre::factory()->for($this->user)->create();
        Candidate::factory()->for($offre)->for($this->user)->create();
        Candidate::factory()->for($offre)->for($this->user)->create();

        $this->actingAs($this->user)
            ->get(route('offres.index'))
            ->assertSee('2 candidatures');
    }

    public function test_create_form_is_displayed(): void
    {
        $this->actingAs($this->user)
            ->get(route('offres.create'))
            ->assertOk()
            ->assertSee('Nouvelle offre');
    }

    public function test_store_creates_offre(): void
    {
        $data = [
            'titre' => 'Développeur Laravel',
            'description' => 'Nous cherchons un développeur Laravel expérimenté.',
            'experience_min' => 3,
        ];

        $this->actingAs($this->user)
            ->post(route('offres.store'), $data)
            ->assertRedirect();

        $this->assertDatabaseHas('offres', [
            'user_id' => $this->user->id,
            'titre' => 'Développeur Laravel',
            'experience_min' => 3,
        ]);
    }

    public function test_store_syncs_competences(): void
    {
        $competence = Competence::factory()->create(['nom' => 'PHP']);

        $data = [
            'titre' => 'Développeur PHP',
            'description' => 'Poste de développeur PHP.',
            'competences' => ['PHP'],
        ];

        $this->actingAs($this->user)
            ->post(route('offres.store'), $data)
            ->assertRedirect();

        $offre = Offre::where('titre', 'Développeur PHP')->first();
        $this->assertTrue($offre->competences->contains('nom', 'PHP'));
    }

    public function test_store_validates_titre_required(): void
    {
        $this->actingAs($this->user)
            ->post(route('offres.store'), [
                'titre' => '',
                'description' => 'Une description suffisamment longue.',
            ])
            ->assertSessionHasErrors('titre');
    }

    public function test_store_validates_description_min_length(): void
    {
        $this->actingAs($this->user)
            ->post(route('offres.store'), [
                'titre' => 'Titre',
                'description' => 'Court',
            ])
            ->assertSessionHasErrors('description');
    }

    public function test_show_displays_offre(): void
    {
        $offre = Offre::factory()->for($this->user)->create(['titre' => 'Offre Test']);

        $this->actingAs($this->user)
            ->get(route('offres.show', $offre))
            ->assertOk()
            ->assertSee('Offre Test');
    }

    public function test_show_displays_competences(): void
    {
        $offre = Offre::factory()->for($this->user)->create();
        $competence = Competence::factory()->create(['nom' => 'Laravel']);
        $offre->competences()->attach($competence);

        $this->actingAs($this->user)
            ->get(route('offres.show', $offre))
            ->assertSee('Laravel');
    }

    public function test_show_displays_candidate_with_score(): void
    {
        $offre = Offre::factory()->for($this->user)->create();
        $candidate = Candidate::factory()->for($offre)->for($this->user)->create();
        $candidate->analyse()->create([
            'matching_score' => 85,
            'recommandation' => 'convoquer',
            'competences_extraites' => [],
            'langues' => [],
            'points_forts' => [],
            'lacunes' => [],
            'competences_manquantes' => [],
        ]);

        $this->actingAs($this->user)
            ->get(route('offres.show', $offre))
            ->assertSee('85/100')
            ->assertSee('À convoquer');
    }

    public function test_show_displays_pending_badge(): void
    {
        $offre = Offre::factory()->for($this->user)->create();
        Candidate::factory()->for($offre)->for($this->user)->create();

        $this->actingAs($this->user)
            ->get(route('offres.show', $offre))
            ->assertSee('En attente');
    }

    public function test_edit_form_is_displayed(): void
    {
        $offre = Offre::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->get(route('offres.edit', $offre))
            ->assertOk()
            ->assertSee('Modifier');
    }

    public function test_update_modifies_offre(): void
    {
        $offre = Offre::factory()->for($this->user)->create(['titre' => 'Ancien titre']);

        $this->actingAs($this->user)
            ->put(route('offres.update', $offre), [
                'titre' => 'Nouveau titre',
                'description' => 'Description mise à jour avec assez de caractères.',
                'experience_min' => 5,
            ])
            ->assertRedirect(route('offres.show', $offre));

        $this->assertDatabaseHas('offres', [
            'id' => $offre->id,
            'titre' => 'Nouveau titre',
        ]);
    }

    public function test_destroy_deletes_offre(): void
    {
        $offre = Offre::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->delete(route('offres.destroy', $offre))
            ->assertRedirect(route('offres.index'));

        $this->assertDatabaseMissing('offres', ['id' => $offre->id]);
    }

    public function test_authorization_prevents_accessing_other_users_offre(): void
    {
        $otherUser = User::factory()->create();
        $offre = Offre::factory()->for($otherUser)->create();

        $this->actingAs($this->user)
            ->get(route('offres.show', $offre))
            ->assertForbidden();
    }

    public function test_authorization_prevents_updating_other_users_offre(): void
    {
        $otherUser = User::factory()->create();
        $offre = Offre::factory()->for($otherUser)->create();

        $this->actingAs($this->user)
            ->put(route('offres.update', $offre), [
                'titre' => 'Hack',
                'description' => 'Tentative de modification non autorisée.',
            ])
            ->assertForbidden();
    }

    public function test_authorization_prevents_deleting_other_users_offre(): void
    {
        $otherUser = User::factory()->create();
        $offre = Offre::factory()->for($otherUser)->create();

        $this->actingAs($this->user)
            ->delete(route('offres.destroy', $offre))
            ->assertForbidden();

        $this->assertDatabaseHas('offres', ['id' => $offre->id]);
    }
}
