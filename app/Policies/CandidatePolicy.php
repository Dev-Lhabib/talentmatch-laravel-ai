<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Candidate;
use App\Models\Offre;
use App\Models\User;

class CandidatePolicy
{
    public function store(User $user, Offre $offre): bool
    {
        return $user->id === $offre->user_id;
    }

    public function view(User $user, Candidate $candidate): bool
    {
        return $user->id === $candidate->offre->user_id;
    }

    public function delete(User $user, Candidate $candidate): bool
    {
        return $user->id === $candidate->offre->user_id;
    }
}
