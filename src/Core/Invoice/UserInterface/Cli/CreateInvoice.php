<?php

namespace App\Core\Invoice\UserInterface\Cli;

use App\Common\Bus\QueryBusInterface;
use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceCommand;
use App\Core\User\Application\Query\GetUserByEmail\GetUserByEmailQuery;
use App\Core\User\Domain\Exception\UserNotFoundException;
use App\Core\User\Domain\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:invoice:create',
    description: 'Dodawanie nowej faktury'
)]
class CreateInvoice extends Command
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            /** @var User $user */
            $user = $this->queryBus->dispatch(new GetUserByEmailQuery($input->getArgument('email')));
        } catch (HandlerFailedException $exception) {
            return $this->handleException($exception, $output);
        }
        if(!$user->isActive()) {
            $output->writeln('Użytkownik jest nieaktywny.');
            return Command::FAILURE;
        }
        $amount = (int) $input->getArgument('amount');
        if($amount <= 0) {
            $output->writeln('Kwota musi być większa od 0.');
            return Command::FAILURE;
        }
        $this->commandBus->dispatch(new CreateInvoiceCommand(
            $user,
            $input->getArgument('amount')
        ));

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED);
        $this->addArgument('amount', InputArgument::REQUIRED);
    }

    private function handleException(HandlerFailedException $exception, OutputInterface $output): int
    {
        foreach ($exception->getNestedExceptions() as $nested) {
            if ($nested instanceof UserNotFoundException) {
                $output->writeln('Użytkownik o podanym adresie e-mail nie istnieje.');
                return Command::FAILURE;
            }
        }
        throw $exception;
    }
}
