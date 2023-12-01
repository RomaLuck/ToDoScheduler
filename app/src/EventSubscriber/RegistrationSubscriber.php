<?php

namespace App\EventSubscriber;

use App\Event\RegistrationEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerInterface       $mailer,
        private readonly UrlGeneratorInterface $router
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function onRegistrationEvent(RegistrationEvent $event): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('example@example.com', 'TodoTeam'))
            ->to($event->getEmail())
            ->subject('Successfully registration')
            ->htmlTemplate('emails/registration_greeting.html.twig');

        $this->mailer->send($email);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RegistrationEvent::NAME => 'onRegistrationEvent',
        ];
    }
}
