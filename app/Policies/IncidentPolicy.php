<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    /**
     * Determine if the user can view any incidents.
     */
    public function viewAny(User $user): bool
    {
        // Only managers can view incident list
        return $user->role === 'manager';
    }

    /**
     * Determine if the user can view the incident.
     */
    public function view(User $user, Incident $incident): bool
    {
        // Only managers can view incident details
        return $user->role === 'manager';
    }

    /**
     * Determine if the user can create incidents.
     */
    public function create(User $user): bool
    {
        // Both employees and managers can create
        return true;
    }

    /**
     * Determine if the user can update the incident.
     */
    public function update(User $user, Incident $incident): bool
    {
        // Only managers can update
        return $user->role === 'manager';
    }

    /**
     * Determine if the user can delete the incident.
     */
    public function delete(User $user, Incident $incident): bool
    {
        // Only managers can delete
        return $user->role === 'manager';
    }
}


