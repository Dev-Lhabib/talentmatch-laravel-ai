<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Candidature;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Candidature>
 */
class CandidatureFactory extends Factory
{
    protected $model = Candidature::class;

    public function definition(): array
    {
        return [
            'offre_id' => Offre::factory(),
            'user_id' => User::factory(),
            'nom_candidat' => fake()->name(),
            'cv_text' => fake()->paragraphs(3, true),
            'status' => 'pending',
        ];
    }
}
