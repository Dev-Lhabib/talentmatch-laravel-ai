<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Offre;
use App\Models\User;

class OffrePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function store(User $user, Offre $offre): bool
    {
        return $user->id === $offre->user_id;
    }

    public function view(User $user, Offre $offre): bool
    {
        return $user->id === $offre->user_id;
    }

    public function update(User $user, Offre $offre): bool
    {
        return $user->id === $offre->user_id;
    }

    public function delete(User $user, Offre $offre): bool
    {
        return $user->id === $offre->user_id;
    }
}
