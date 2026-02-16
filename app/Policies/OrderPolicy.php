<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage_orders');
    }

    public function view(User $user, Order $order): bool
    {
        return $user->can('manage_orders');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_orders');
    }

    public function update(User $user, Order $order): bool
    {
        return $user->can('manage_orders');
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->can('manage_orders');
    }

    public function restore(User $user, Order $order): bool
    {
        return $user->can('manage_orders');
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return $user->can('manage_orders');
    }
}
