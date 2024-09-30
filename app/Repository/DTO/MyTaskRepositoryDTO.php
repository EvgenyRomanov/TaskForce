<?php

namespace App\Repository\DTO;

use App\Models\User;

readonly class MyTaskRepositoryDTO
{
    public function __construct(
        public User $user,
        public ?string $status = null,
    ){
    }
}
