<?php

namespace App\Core\User\Application\Query\GetUserByEmail;

use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetUserByEmailHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(GetUserByEmailQuery $query): User
    {
        return $this->userRepository->getByEmail($query->email);
    }
}