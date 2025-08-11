<?php

namespace App\Core\User\Application\Query\SelectUsersEmailsByStatus;

use App\Core\User\Domain\Aggregate\EmailCollection;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SelectUsersEmailsByStatusHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(SelectUsersEmailsByStatusQuery $query): EmailCollection
    {
        return $this->userRepository->findUsersEmailByStatus($query->status);
    }
}