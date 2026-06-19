<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Candidate>
 */
class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    public function definition(): array
    {
        return [
            'offre_id' => Offre::factory(),
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'cv_text' => fake()->paragraphs(3, true),
            'status' => 'pending',
        ];
    }
}
