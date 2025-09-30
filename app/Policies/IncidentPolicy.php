<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    public function update(User $user, Incident $incident): bool
    {
        return $user->id === $incident->user_id || $user->role === 'manager';
    }

    public function delete(User $user, Incident $incident): bool
    {
        return $user->id === $incident->user_id || $user->role === 'manager';
    }
}


