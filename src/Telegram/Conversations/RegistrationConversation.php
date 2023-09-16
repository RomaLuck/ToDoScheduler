<?php

namespace App\Telegram\Conversations;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationConversation extends Conversation
{
    protected ?string $step = 'askEmail';
    protected string $email;

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface      $entityManager
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function askEmail(Nutgram $bot): void
    {
        $bot->sendMessage('Enter your email');
        $this->next('checkEmail');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function checkEmail(Nutgram $bot): void
    {
        $text = $bot->message()->text ?? '';
//        if ($text === 'exit') {
//            $this->setSkipHandlers(true)->end();
//        }
        if (preg_match('!^[\w\-.]+@([\w\-]+.)+[\w\-]{2,4}$!iu', $text, $validatedEmail)) {
            $this->email = $validatedEmail[0];
            $bot->sendMessage('Enter your password');
            $this->next('checkPassword');
        } else {
            $bot->sendMessage('Email is invalid');
            $this->askEmail($bot);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function checkPassword(Nutgram $bot): void
    {
        $user = new User();
        $user->setEmail($this->email);
        $password = $this->userPasswordHasher->hashPassword($user, trim(htmlspecialchars($bot->message()->text)));
        $user->setPassword($password);
        $user->setRoles(['ROLE_USER']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $bot->sendMessage('User has been registered successfully');
        $this->end();
    }
}