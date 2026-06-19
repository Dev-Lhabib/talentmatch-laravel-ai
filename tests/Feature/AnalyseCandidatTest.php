<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Ai\Agents\AnalyseCandidatAnalysisAgent;
use App\Enums\StatutCandidatureEnum;
use App\Jobs\AnalyseCandidatJob;
use App\Models\Analyse;
use App\Models\Candidature;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyseCandidatTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Offre $offre;

    private Candidature $candidature;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->offre = Offre::factory()->for($this->user)->create([
            'titre' => 'Développeur Laravel',
            'description' => 'Nous recherchons un développeur Laravel expérimenté.',
            'experience_min' => 3,
        ]);
        $this->candidature = Candidature::factory()->for($this->offre)->for($this->user)->create([
            'nom_candidat' => 'Jean Dupont',
            'cv_text' => "Compétences techniques : PHP, Laravel, MySQL, Git, Docker, Vue.js\n\n"
                ."Expérience professionnelle : Développeur PHP chez TechCorp (2020-2024)\n\n"
                ."Formation : Master en Informatique\n\n"
                .'Langues : Français (natif), Anglais (courant)',
        ]);
    }

    public function test_analysis_success_creates_analyse_record(): void
    {
        AnalyseCandidatAnalysisAgent::fake();

        AnalyseCandidatJob::dispatchSync($this->candidature);

        $this->candidature->refresh();

        $this->assertSame(StatutCandidatureEnum::Completed, $this->candidature->status);

        $analyse = Analyse::where('candidature_id', $this->candidature->id)->first();
        $this->assertNotNull($analyse);
        $this->assertNotNull($analyse->matching_score);
        $this->assertIsArray($analyse->competences_extraites);
        $this->assertIsArray($analyse->points_forts);
        $this->assertIsArray($analyse->lacunes);
        $this->assertIsArray($analyse->competences_manquantes);
        $this->assertIsArray($analyse->langues);
        $this->assertNotNull($analyse->justification);
        $this->assertNotNull($analyse->recommandation);
        $this->assertNotNull($analyse->analyzed_at);
    }

    public function test_analysis_fails_when_api_key_missing(): void
    {
        config()->set('ai.default', 'openai');

        AnalyseCandidatJob::dispatchSync($this->candidature);

        $this->candidature->refresh();

        $this->assertSame(StatutCandidatureEnum::Failed, $this->candidature->status);
        $this->assertDatabaseMissing('analyses', [
            'candidature_id' => $this->candidature->id,
        ]);
    }

    public function test_analysis_agent_exception_is_rethrown(): void
    {
        AnalyseCandidatAnalysisAgent::fake(function () {
            throw new \RuntimeException('AI provider returned an invalid response');
        });

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('AI provider returned an invalid response');

        AnalyseCandidatJob::dispatchSync($this->candidature);

        $this->candidature->refresh();
        $this->assertSame(StatutCandidatureEnum::Processing, $this->candidature->status);
    }
}
