<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Auth\Access\HandlesAuthorization;

class VoucherPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('manage_vouchers');
    }

    public function view(User $user, Voucher $voucher): bool
    {
        return $user->can('manage_vouchers');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_vouchers');
    }

    public function update(User $user, Voucher $voucher): bool
    {
        return $user->can('manage_vouchers');
    }

    public function delete(User $user, Voucher $voucher): bool
    {
        return $user->can('manage_vouchers');
    }

    public function restore(User $user, Voucher $voucher): bool
    {
        return $user->can('manage_vouchers');
    }

    public function forceDelete(User $user, Voucher $voucher): bool
    {
        return $user->can('manage_vouchers');
    }
}
