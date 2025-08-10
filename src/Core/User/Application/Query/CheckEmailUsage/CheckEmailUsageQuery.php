<?php

namespace App\Core\User\Application\Query\CheckEmailUsage;

class CheckEmailUsageQuery
{
    public function __construct(
        public readonly string $email
    ) {}
}