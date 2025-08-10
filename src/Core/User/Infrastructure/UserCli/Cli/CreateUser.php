<?php

namespace App\Core\User\Infrastructure\UserCli\Cli;

use App\Common\Bus\QueryBusInterface;
use App\Core\User\Application\Command\CreateInactiveUser\CreateInactiveUserCommand;
use App\Core\User\Application\Query\CheckEmailUsage\CheckEmailUsageQuery;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:user:create-new',
    description: 'Nowy nieaktywny użytkownik na podstawie e-maila'
)]
class CreateUser extends Command
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly MessageBusInterface $commandBus,
    )
    {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('e-mail');
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $output->writeln('Niepoprawny adres e-mail');
            return Command::FAILURE;
        }
        $emailUsage = $this->queryBus->dispatch(new CheckEmailUsageQuery($email));
        if($emailUsage) {
            $output->writeln('Adres e-mail jest już w użyciu');
            return Command::FAILURE;
        }
        $this->commandBus->dispatch(new CreateInactiveUserCommand($email));
        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('e-mail', InputArgument::REQUIRED);
    }
}