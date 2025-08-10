<?php

namespace App\Core\User\Domain\Event;

use App\Common\EventManager\EventInterface;
use App\Core\User\Domain\User;

class UserRegisteredEvent implements EventInterface
{
    public const SUBJECT = 'Konto zostało zarejestrowana';
    public const CONTENT = 'Zarejestrowano konto w systemie. Aktywacja konta trwa do 24h';
    public function __construct(
        public readonly User $user
    ) {}
}