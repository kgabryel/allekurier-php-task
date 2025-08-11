<?php

namespace App\Core\User\Domain;

enum UserStatus
{
    case ACTIVE;
    case INACTIVE;
}