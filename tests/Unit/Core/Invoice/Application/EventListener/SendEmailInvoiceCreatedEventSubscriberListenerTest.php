<?php

namespace App\Tests\Unit\Core\Invoice\Application\EventListener;

use App\Core\Invoice\Application\EventListener\SendEmailInvoiceCreatedEventSubscriberListener;
use App\Core\Invoice\Domain\Event\InvoiceCreatedEvent;
use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Notification\NotificationInterface;
use App\Core\User\Domain\User;
use PHPUnit\Framework\TestCase;

class SendEmailInvoiceCreatedEventSubscriberListenerTest extends TestCase
{
    /**
     * @Powinien zostać wysłany e-mail - zostać wywołany notificator z odpowiednimi parametrami
     */
    public function test_handle_success(): void
    {
        //Arrange
        $userEmail = 'test@email.com';
        $user = new User($userEmail, true);
        $invoice = new Invoice($user, 100);

        $mailer = $this->createMock(NotificationInterface::class);
        $mailer->expects($this->once())
            ->method('sendEmail')
            ->with(
                $userEmail,
                'Utworzono fakturę',
                'Dla twojego konta została wystawiona faktura'
            );

        $listener = new SendEmailInvoiceCreatedEventSubscriberListener($mailer);
        $event = new InvoiceCreatedEvent($invoice);

        //Act
        $listener->send($event);
    }
}