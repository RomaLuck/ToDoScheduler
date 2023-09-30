<?php

namespace App\EventSubscriber;

use App\Event\RegistrationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RegistrationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerInterface                     $mailer,
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function onRegistrationEvent(RegistrationEvent $event): void
    {
        $email = (new Email())
            ->to($event->getEmail())
            ->subject('Successfully registration')
            ->text('Thank you for registration! Check out our web site "here is website"');
        $this->mailer->send($email);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RegistrationEvent::NAME => 'onRegistrationEvent',
        ];
    }
}
