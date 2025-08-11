<?php

namespace App\Core\User\Application\Command\CreateInactiveUser;

class CreateInactiveUserCommand
{
    public function __construct(
        public readonly string $email
    ) {}
}