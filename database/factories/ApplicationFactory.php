<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Offre;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'offre_id' => Offre::factory(),
            'status' => 'pending',
        ];
    }
}
