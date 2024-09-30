<?php

namespace App\Actions\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\AuthManager;

class IsExecutorAction
{
    public function __invoke(AuthManager $authManager): void
    {
        /** @var User $user */
        $user = $authManager->user();
        if (is_null($user)) abort(401);
        if ($user->role->name === Role::CUSTOMER) abort(403);
    }
}
