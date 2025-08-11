<?php

namespace App\Tests\Integration\Core\Invoice\UserInterface\Cli;

use App\Core\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CreateInvoiceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private CommandTester $tester;
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);

        $application = new Application(self::$kernel);
        $command = $application->find('app:invoice:create');

        $this->tester = new CommandTester($command);
    }

    /**
     * @testdox Próba dodania faktura dla nieistniejącego użytkownika powinna zakończyć się błędem
     */
    public function test_non_exists_user(): void
    {
        //Act
        $this->tester->execute([
            'email' => 'test@email.com',
            'amount' => '100'
        ]);

        //Assert
        $this->assertSame(Command::FAILURE, $this->tester->getStatusCode());
        $this->assertSame(
            'Użytkownik o podanym adresie e-mail nie istnieje.',
            trim($this->tester->getDisplay())
        );
    }

    /**
     * @testdox Próba dodania faktura dla nieaktywnego użytkownika powinna zakończyć się błędem
     */
    public function test_inactive_user(): void
    {
        //Arrange
        $this->createUser('test@email.com', false);

        //Act
        $this->tester->execute([
            'email' => 'test@email.com',
            'amount' => '100'
        ]);

        //Assert
        $this->assertSame(Command::FAILURE, $this->tester->getStatusCode());
        $this->assertSame(
            'Użytkownik jest nieaktywny.',
            trim($this->tester->getDisplay())
        );
    }

    /**
     * @dataProvider \App\Tests\DataProvider\InvoiceAmountDataProvider::invalidAmountProvider
     * @testdox Próba dodania faktury z błędną kwotą powinna zakończyć się błędem
     */
    public function test_invalid_amount(int $amount): void
    {
        //Arrange
        $this->createUser('test@email.com', true);

        //Act
        $this->tester->execute([
            'email' => 'test@email.com',
            'amount' => $amount
        ]);

        //Assert
        $this->assertSame(Command::FAILURE, $this->tester->getStatusCode());
        $this->assertSame(
            'Kwota musi być większa od 0.',
            trim($this->tester->getDisplay())
        );
    }

    /**
     * @testdox Podczas poprawnego dodania faktury nie powinien pojawić się błąd
     */
    public function test_success_scenario(): void
    {
        //Arrange
        $this->createUser('test@email.com', true);

        //Act
        $this->tester->execute([
            'email' => 'test@email.com',
            'amount' => '10000'
        ]);

        $this->assertTrue(true);

        //Assert
        $this->assertSame(Command::SUCCESS, $this->tester->getStatusCode());
    }

    private function createUser(string $email, bool $status): void
    {
        $user = new User($email, $status);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}