<?php

namespace App\Core\User\Application\Query\CheckEmailUsage;

use App\Core\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CheckEmailUsageHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(CheckEmailUsageQuery $query): bool
    {
        return $this->userRepository->checkEmailUsage($query->email);
    }
}