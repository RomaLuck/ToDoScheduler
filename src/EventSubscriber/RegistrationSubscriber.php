<?php

namespace App\EventSubscriber;

use App\Event\RegistrationEvent;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
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
        $email = (new NotificationEmail())
            ->to($event->getEmail())
            ->subject('Successfully registration')
            ->htmlTemplate('emails/comment_notification.html.twig')
            ->action(
                'Check out our website',
                $this->router->generate('app_task', [], UrlGeneratorInterface::ABSOLUTE_URL)
            )
            ->content('Thank you for registration! I am happy to see you!');
        $this->mailer->send($email);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RegistrationEvent::NAME => 'onRegistrationEvent',
        ];
    }
}
