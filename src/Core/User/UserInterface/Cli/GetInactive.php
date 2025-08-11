<?php

namespace App\Core\User\UserInterface\Cli;

use App\Common\Bus\QueryBusInterface;
use App\Core\User\Application\Query\SelectUsersEmailsByStatus\SelectUsersEmailsByStatusQuery;
use App\Core\User\Domain\Aggregate\EmailCollection;
use App\Core\User\Domain\UserStatus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:user:get-inactive',
    description: 'Pobiera listę adresów e-mail nieaktywnych użytkowników'
)]
class GetInactive extends Command
{
    public function __construct(private readonly QueryBusInterface $bus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var EmailCollection $emails
         */
        $emails = $this->bus->dispatch(new SelectUsersEmailsByStatusQuery(UserStatus::INACTIVE));

        foreach ($emails as $email) {
            $output->writeln($email);
        }
        return Command::SUCCESS;
    }
}