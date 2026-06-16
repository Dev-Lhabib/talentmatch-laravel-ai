<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Candidature;
use App\Models\Offre;
use App\Models\User;

class CandidaturePolicy
{
    public function store(User $user, Offre $offre): bool
    {
        return $user->id === $offre->user_id;
    }

    public function view(User $user, Candidature $candidature): bool
    {
        return $user->id === $candidature->offre->user_id;
    }

    public function delete(User $user, Candidature $candidature): bool
    {
        return $user->id === $candidature->offre->user_id;
    }
}
