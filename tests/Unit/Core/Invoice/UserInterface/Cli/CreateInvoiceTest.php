<?php

namespace App\Tests\Unit\Core\Invoice\UserInterface\Cli;

use App\Common\Bus\QueryBus;
use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceCommand;
use App\Core\Invoice\UserInterface\Cli\CreateInvoice;
use App\Core\User\Application\Query\GetUserByEmail\GetUserByEmailQuery;
use App\Core\User\Domain\Exception\UserNotFoundException;
use App\Core\User\Domain\User;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateInvoiceTest extends TestCase
{
    private const TEST_EMAIL = 'test@email.com';
    private const TEST_AMOUNT = 100000;

    /**
     * @testdox Dodanie nowej faktury powinno zakończyć się sukcesem
     */
    public function test_execute_success(): void
    {
        //Arrange
        $commandBus = $this->createMock(MessageBusInterface::class);

        $commandBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(CreateInvoiceCommand::class))
            ->willReturn(new Envelope(new stdClass()));

        $queryBus = $this->createQueryBusWithReturnValue(new User(self::TEST_EMAIL, true));

        $command = new CreateInvoice($commandBus, $queryBus);
        $tester = new CommandTester($command);

        //Act
        $tester->execute([
            'email' => self::TEST_EMAIL,
            'amount' => self::TEST_AMOUNT
        ]);

        //Assert
        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
    }

    /**
     * @testdox Próba dodania faktura dla nieistniejącego użytkownika powinna zakończyć się błędem
     */
    public function test_non_exists_user(): void
    {
        //Arrange
        $commandBus = $this->createCommandBusWithoutDispatch();

        $queryBus = $this->createStub(QueryBus::class);
        $queryBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(GetUserByEmailQuery::class))
            ->willThrowException(
                new HandlerFailedException(new Envelope(new stdClass()), [new UserNotFoundException()])
            );

        $command = new CreateInvoice($commandBus, $queryBus);
        $tester = new CommandTester($command);

        //Act
        $tester->execute([
            'email' => self::TEST_EMAIL,
            'amount' => self::TEST_AMOUNT
        ]);

        //Assert
        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertSame(
            'Użytkownik o podanym adresie e-mail nie istnieje.',
            trim($tester->getDisplay())
        );
    }

    /**
     * @testdox Próba dodania faktura dla nieaktywnego użytkownika powinna zakończyć się błędem
     */
    public function test_inactive_user(): void
    {
        //Arrange
        $commandBus = $this->createCommandBusWithoutDispatch();

        $queryBus = $this->createQueryBusWithReturnValue(new User(self::TEST_EMAIL, false));

        $command = new CreateInvoice($commandBus, $queryBus);
        $tester = new CommandTester($command);

        //Act
        $tester->execute([
            'email' => self::TEST_EMAIL,
            'amount' => self::TEST_AMOUNT
        ]);

        //Assert
        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertSame('Użytkownik jest nieaktywny.', trim($tester->getDisplay()));
    }

    /**
     * @dataProvider \App\Tests\DataProvider\InvoiceAmountDataProvider::invalidAmountProvider
     * @testdox Próba dodania faktury z błędną kwotą powinna zakończyć się błędem
     */
    public function test_invalid_amount(int $amount): void
    {
        //Arrange
        $commandBus = $this->createCommandBusWithoutDispatch();

        $queryBus = $this->createQueryBusWithReturnValue(new User(self::TEST_EMAIL, true));

        $command = new CreateInvoice($commandBus, $queryBus);
        $tester = new CommandTester($command);

        //Act
        $tester->execute([
            'email' => self::TEST_EMAIL,
            'amount' => $amount
        ]);

        //Assert
        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertSame('Kwota musi być większa od 0.', trim($tester->getDisplay()));
    }

    /**
     * @dataProvider missingArgumentsProvider
     * @testdox Próba dodania faktury z błędną kwotą powinna zakończyć się błędem
     */
    public function test_invalid_args(array $arguments, string $message): void
    {
        //Arrange
        $commandBus = $this->createCommandBusWithoutDispatch();

        $queryBus = $this->createStub(QueryBus::class);

        $command = new CreateInvoice($commandBus, $queryBus);
        $tester = new CommandTester($command);

        //Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($message);

        //Act
        $tester->execute($arguments);
    }

    public static function missingArgumentsProvider(): array
    {
        return [
            [
                'arguments' => [],
                'message' => 'Not enough arguments (missing: "email, amount")'
            ],
            [
                'arguments' => [
                    'email' => 'email'
                ],
                'message' => 'Not enough arguments (missing: "amount").'
            ],
            [
                'arguments' => [
                    'amount' => 'amount'
                ],
                'message' => 'Not enough arguments (missing: "email").'
            ]
        ];
    }

    private function createCommandBusWithoutDispatch(): MessageBusInterface
    {
        $commandBus = $this->createMock(MessageBusInterface::class);

        $commandBus->expects($this->never())->method('dispatch');
        return $commandBus;
    }

    private function createQueryBusWithReturnValue(User $user): QueryBus
    {
        $queryBus = $this->createMock(QueryBus::class);
        $queryBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(GetUserByEmailQuery::class))
            ->willReturn($user);
        return $queryBus;
    }
}