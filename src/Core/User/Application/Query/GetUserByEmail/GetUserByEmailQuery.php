<?php

namespace App\Core\User\Application\Query\GetUserByEmail;

class GetUserByEmailQuery
{
    public function __construct(
        public readonly  string $email
    ) {}
}