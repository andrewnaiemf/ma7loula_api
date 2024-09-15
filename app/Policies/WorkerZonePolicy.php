<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkerZone;
use Illuminate\Auth\Access\Response;

class WorkerZonePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkerZone $WorkerZone): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkerZone $WorkerZone): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkerZone $WorkerZone): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkerZone $WorkerZone): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkerZone $WorkerZone): bool
    {
        //
    }
}
