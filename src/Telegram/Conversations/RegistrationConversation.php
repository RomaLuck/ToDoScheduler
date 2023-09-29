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
        if ($this->entityManager->getRepository(User::class)->findOneBy(['chat_id' => $bot->chatId()]) !== null) {
            $bot->sendMessage('User has been already registered');
            return;
        }
        $bot->sendMessage(
            text: 'Enter your email',
            reply_markup: ReplyKeyboardMarkup::make(
                resize_keyboard: true,
                one_time_keyboard: true,
            )
                ->addRow(KeyboardButton::make('Exit'))
        );
        $this->next('checkEmail');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function checkEmail(Nutgram $bot): void
    {
        $text = $bot->message()->text ?? '';
        switch (true) {
            case false !== stripos($text, "Exit"):
                $this->end();
                break;
            case !preg_match('!^[\w\-.]+@([\w\-]+.)+[\w\-]{2,4}$!iu', $text, $validatedEmail):
                $bot->sendMessage('Email is invalid');
                $this->askEmail($bot);
                break;
            case $this->entityManager->getRepository(User::class)->findOneBy(['email' => $validatedEmail[0]]) !== null:
                $bot->sendMessage('Such email already exists. Try again, please');
                $this->askEmail($bot);
                break;
            default:
                $this->email = $validatedEmail[0];
                $bot->sendMessage('Enter your password');
                $this->next('checkPassword');
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