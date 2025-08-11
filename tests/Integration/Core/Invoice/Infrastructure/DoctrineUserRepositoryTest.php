<?php

namespace App\Tests\Integration\Core\Invoice\Infrastructure;

use App\Core\User\Domain\Exception\UserNotFoundException;
use App\Core\User\Domain\User;
use App\Core\User\Infrastructure\Persistance\DoctrineUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineUserRepositoryTest extends KernelTestCase
{
    private DoctrineUserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->userRepository = self::getContainer()->get(DoctrineUserRepository::class);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);
    }

    /**
     * @testdox W przypadku próby pobrania użytnika, bez danych w bazie, powinien zostać rzucony wyjątek
     */
    public function test_empty_data(): void
    {
        //Assert
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Użytkownik nie istnieje');

        //Act
        $this->userRepository->getByEmail('test@email.com');
    }

    /**
     * @testdox W przupadku próby pobraniu użytkownika, który nie znajduje się bazie powinien zostać rzucony wyjątek
     */
    public function test_non_exists_email(): void
    {
        //Arrange
        $this->createUser('test1@email.com');
        $this->createUser('test2@email.com');
        $this->createUser('test3@email.com');

        //Assert
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Użytkownik nie istnieje');

        //Act
        $this->userRepository->getByEmail('test@email.com');
    }

    /**
     * @testdox Powinien zostać pobrany poprawny użytkownik
     */
    public function test_correct_find(): void
    {
        //Arrange
        $this->createUser('test1@email.com');
        $this->createUser('test2@email.com');
        $this->createUser('test3@email.com');
        $this->createUser('test@email.com');

        //Act
        $user = $this->userRepository->getByEmail('test@email.com');

        //Assert
        $this->assertEquals('test@email.com', $user->getEmail());
    }

    private function createUser(string $email): void
    {
        $user = new User($email, true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}