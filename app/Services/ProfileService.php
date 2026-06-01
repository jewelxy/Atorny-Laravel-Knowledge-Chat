<?php

namespace App\Services;

use App\Models\User;

class ProfileService
{
    /**
     * Return the authenticated user profile model.
     */
    public function profile(User $user): User
    {
        return $user;
    }
}