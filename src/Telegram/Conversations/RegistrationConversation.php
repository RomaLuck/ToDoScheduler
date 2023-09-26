<?php

namespace App\Telegram\Conversations;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
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
     * @throws \Exception
     */
    public function askEmail(Nutgram $bot): void
    {
        if (!in_array(
            $bot->chatId(),
            array_map(static fn($user) => $user->getChatId(), $this->entityManager->getRepository(User::class)->findAll())
        )) {
            $bot->sendMessage(
                text: 'Enter your email',
                reply_markup: ReplyKeyboardMarkup::make(
                    resize_keyboard: true,
                    one_time_keyboard: true,
                )
                    ->addRow(KeyboardButton::make('Exit'))
            );
            $this->next('checkEmail');
        } else {
            throw new \Exception('User has been already registered');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function checkEmail(Nutgram $bot): void
    {
        $text = $bot->message()->text ?? '';
        if (false !== stripos($text, "Exit")) {
            $this->end();
        } elseif (preg_match('!^[\w\-.]+@([\w\-]+.)+[\w\-]{2,4}$!iu', $text, $validatedEmail)) {
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
        $user->setChatId((string)$bot->chatId());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $bot->sendMessage(
            text: "User has been registered successfully \xF0\x9F\x8E\x89",
            reply_markup: ReplyKeyboardMarkup::make(
                resize_keyboard: true,
            )
                ->addRow(
                    KeyboardButton::make("\xF0\x9F\x95\x9B Create new task"),
                    KeyboardButton::make("\xF0\x9F\x94\xA5 My tasks")
                )
        );
        $this->end();
    }
}