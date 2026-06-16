<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Offre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offre>
 */
class OffreFactory extends Factory
{
    protected $model = Offre::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'titre' => fake()->sentence(3),
            'description' => fake()->paragraph(2),
            'experience_min' => fake()->numberBetween(0, 10),
        ];
    }
}
