<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class CandidatePolicy
{
    public function store(User $user, Application $application): bool
    {
        return $user->id === $application->offre->user_id;
    }

    public function view(User $user, Application $application): bool
    {
        return $user->id === $application->offre->user_id;
    }

    public function delete(User $user, Application $application): bool
    {
        return $user->id === $application->offre->user_id;
    }
}
