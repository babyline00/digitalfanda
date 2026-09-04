<?php

namespace App\Policies;

use App\Models\Seller;
use App\Models\User;

class SellerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function view(User $user, Seller $seller): bool
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }
        return $user->seller?->id === $seller->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'customer' || $user->isAdmin() || $user->isSuperAdmin();
    }

    public function update(User $user, Seller $seller): bool
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }
        return $user->seller?->id === $seller->id;
    }

    public function approve(User $user, Seller $seller): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function suspend(User $user, Seller $seller): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function managePayouts(User $user, Seller $seller): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }
}