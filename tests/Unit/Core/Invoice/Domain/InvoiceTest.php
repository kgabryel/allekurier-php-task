<?php

namespace App\Tests\Unit\Core\Invoice\Domain;

use App\Core\Invoice\Domain\Event\InvoiceCreatedEvent;
use App\Core\Invoice\Domain\Exception\InvoiceException;
use App\Core\Invoice\Domain\Invoice;
use App\Core\User\Domain\User;
use PHPUnit\Framework\TestCase;

class InvoiceTest extends TestCase
{

    /**
     * @testdox Podczas utworzenia nowej faktury powinien zostać wywołany event o tym informujący
     */
    public function test_handle_success(): void
    {
        //Arrange
        $user = $this->createStub(User::class);
        $amount = 100000;

        //Act
        $invoice = new Invoice($user, $amount);

        //Assert
        $events = $invoice->getEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(InvoiceCreatedEvent::class, $events[0]);
    }

    /**
     * @dataProvider \App\Tests\DataProvider\InvoiceAmountDataProvider::invalidAmountProvider
     * @testdox Podczas próby utworzenia faktury z błędną kwotą powinien zostać rzucony wyjątek
     */
    public function test_non_positive_amount(int $amount): void
    {
        //Arrange
        $user = $this->createStub(User::class);

        //Assert
        $this->expectException(InvoiceException::class);

        //Act
        new Invoice($user, $amount);
    }
}