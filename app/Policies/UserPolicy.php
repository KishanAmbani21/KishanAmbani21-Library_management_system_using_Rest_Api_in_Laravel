<?php

namespace App\Policies;

use App\Enums\RolesEnum;
use App\Models\User;

class UserPolicy
{

    /**
     * viewAny
     *
     * @param  mixed $user
     * @return void
     */
    public function viewAny(User $user)
    {
        return in_array($user->roles, [
            RolesEnum::SUPER_ADMIN->value,
            RolesEnum::ADMIN->value,
            RolesEnum::USER->value,
        ]);
    }

    /**
     * createBorrow
     *
     * @param  mixed $user
     * @return void
     */
    public function createBorrow(User $user)
    {
        return in_array($user->roles, [
            RolesEnum::USER->value,
        ]);
    }

    /**
     * crud
     *
     * @param  mixed $user
     * @return void
     */
    public function crud(User $user)
    {
        return in_array($user->roles, [
            RolesEnum::SUPER_ADMIN->value,
            RolesEnum::ADMIN->value
        ]);
    }
}
