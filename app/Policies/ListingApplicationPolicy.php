<?php

namespace App\Policies;

use App\Models\ListingApplication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ListingApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ListingApplication $listingApplication): bool
    {
        // dd($listingApplication->listing->owner, $user->id);
        return $listingApplication->listing->owner->id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ListingApplication $listingApplication): bool
    {
        return $listingApplication->listing->owner->id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ListingApplication $listingApplication): bool
    {
        return $listingApplication->listing->owner->id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ListingApplication $listingApplication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ListingApplication $listingApplication): bool
    {
        return false;
    }
}
