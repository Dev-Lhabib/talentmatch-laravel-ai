<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\RecommandationEnum;
use App\Enums\StatutCandidatureEnum;
use App\Models\Analyse;
use App\Models\Candidature;
use App\Services\AnalyseCandidatService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class AnalyseCandidatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(
        public Candidature $candidature,
    ) {}

    public function handle(AnalyseCandidatService $service): void
    {
        $this->candidature->update(['status' => StatutCandidatureEnum::Processing]);

        $provider = config('ai.default', 'groq');
        $apiKeyEnv = strtoupper($provider).'_API_KEY';

        if (! env($apiKeyEnv)) {
            $this->candidature->update(['status' => StatutCandidatureEnum::Failed]);
            Log::error('AnalyseCandidatJob: AI provider API key not configured', [
                'candidature_id' => $this->candidature->id,
                'provider' => $provider,
                'expected_env_var' => $apiKeyEnv,
            ]);

            return;
        }

        try {
            $result = $service->analyser($this->candidature);

            Analyse::create([
                'candidature_id' => $this->candidature->id,
                'competences_extraites' => $result->get('competences_extraites'),
                'annees_experience' => $result->get('annees_experience'),
                'niveau_etudes' => $result->get('niveau_etudes'),
                'langues' => $result->get('langues'),
                'matching_score' => $result->get('matching_score'),
                'points_forts' => $result->get('points_forts'),
                'lacunes' => $result->get('lacunes'),
                'competences_manquantes' => $result->get('competences_manquantes'),
                'recommandation' => RecommandationEnum::from($result->get('recommandation')),
                'justification' => $result->get('justification'),
                'analyzed_at' => now(),
            ]);

            $this->candidature->update(['status' => StatutCandidatureEnum::Completed]);
        } catch (Throwable $e) {
            if ($this->attempts() >= $this->tries) {
                $this->candidature->update(['status' => StatutCandidatureEnum::Failed]);
                Log::error('AnalyseCandidatJob failed — all retries exhausted', [
                    'candidature_id' => $this->candidature->id,
                    'attempts' => $this->attempts(),
                    'error_message' => $e->getMessage(),
                    'error_trace' => (string) $e,
                ]);
            } else {
                Log::warning('AnalyseCandidatJob failed — will retry', [
                    'candidature_id' => $this->candidature->id,
                    'attempts' => $this->attempts(),
                    'error_message' => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }
}
