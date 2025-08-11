<?php

namespace App\Core\User\Domain\Repository;

use App\Core\User\Domain\Aggregate\EmailCollection;
use App\Core\User\Domain\Exception\UserNotFoundException;
use App\Core\User\Domain\User;
use App\Core\User\Domain\UserStatus;

interface UserRepositoryInterface
{
    /**
     * @throws UserNotFoundException
     */
    public function getByEmail(string $email): User;

    public function checkEmailUsage(string $email): bool;

    public function save(User $user): void;

    public function flush(): void;
    public function findUsersEmailByStatus(UserStatus $userStatus): EmailCollection;

}
