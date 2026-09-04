<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSeller() || $user->isAdmin() || $user->isSuperAdmin() || $user->isCashier();
    }

    public function view(User $user, Order $order): bool
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }
        if ($user->isSeller()) {
            return $order->items()->where('seller_id', $user->seller->id)->exists();
        }
        if ($user->isCashier()) {
            return $order->source === 'pos';
        }
        return $order->user_id === $user->id || $order->email === $user->email;
    }

    public function refund(User $user, Order $order): bool
    {
        return ($user->isAdmin() || $user->isSuperAdmin()) && $order->canBeRefunded();
    }

    public function markPaid(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->isCashier();
    }
}