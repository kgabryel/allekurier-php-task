<?php

namespace App\Core\User\Application\Command;

use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateInactiveUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(CreateInactiveUserCommand $command): void
    {
        $this->userRepository->save(new User($command->email, false));

        $this->userRepository->flush();
    }
}