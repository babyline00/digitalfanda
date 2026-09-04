<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Product $product): bool
    {
        if ($product->isPublished()) {
            return true;
        }
        return $user->isSuperAdmin() || $user->isAdmin() || ($user->isSeller() && $user->seller?->id === $product->seller_id);
    }

    public function create(User $user): bool
    {
        return $user->isSeller() && $user->seller?->isApproved() || $user->isAdmin() || $user->isSuperAdmin();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || ($user->isSeller() && $user->seller?->id === $product->seller_id);
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || ($user->isSeller() && $user->seller?->id === $product->seller_id && !$product->sales_count);
    }

    public function manageModeration(User $user): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }
}