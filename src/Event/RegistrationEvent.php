<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RegistrationEvent extends Event
{
    public const NAME = 'registration.event';
    private string $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}