<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Competence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Competence>
 */
class CompetenceFactory extends Factory
{
    protected $model = Competence::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->unique()->word(),
        ];
    }
}
