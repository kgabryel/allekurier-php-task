<?php

namespace App\Core\User\Application\EventListener;

use App\Core\Invoice\Domain\Notification\NotificationInterface;
use App\Core\User\Domain\Event\UserRegisteredEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserRegisteredListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly NotificationInterface $mailer
    ) {}

    public function send(UserRegisteredEvent $event): void
    {
        $this->mailer->sendEmail(
            $event->user->getEmail(),
            UserRegisteredEvent::SUBJECT,
            UserRegisteredEvent::CONTENT
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::class => 'send'
        ];
    }
}