<?php

namespace App\Core\User\Application\Query\SelectUsersEmailsByStatus;

use App\Core\User\Domain\UserStatus;

class SelectUsersEmailsByStatusQuery
{
    public function __construct(
        public readonly UserStatus $status
    ) {}
}