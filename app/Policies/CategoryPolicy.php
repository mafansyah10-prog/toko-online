<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage_categories');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->can('manage_categories');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_categories');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->can('manage_categories');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->can('manage_categories');
    }

    public function restore(User $user, Category $category): bool
    {
        return $user->can('manage_categories');
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return $user->can('manage_categories');
    }
}
