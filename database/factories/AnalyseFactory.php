<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Analyse;
use App\Models\Candidate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Analyse>
 */
class AnalyseFactory extends Factory
{
    protected $model = Analyse::class;

    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'competences_extraites' => [],
            'annees_experience' => fake()->numberBetween(0, 15),
            'niveau_etudes' => fake()->randomElement(['Bac', 'Bac+2', 'Bac+3', 'Bac+5']),
            'langues' => ['Français'],
            'matching_score' => fake()->numberBetween(0, 100),
            'points_forts' => [],
            'lacunes' => [],
            'competences_manquantes' => [],
            'recommandation' => fake()->randomElement(['convoquer', 'attente', 'rejeter']),
            'justification' => fake()->paragraph(),
            'analyzed_at' => now(),
        ];
    }
}
