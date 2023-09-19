<?php

namespace App\Telegram\Conversations;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
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
            $bot->sendMessage('Enter your email or write "exit"');
            $this->next('checkEmail');
        } else {
            throw new \Exception('User is already registered');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function checkEmail(Nutgram $bot): void
    {
        $text = $bot->message()->text ?? '';
        if (false !== stripos($text, "exit")) {
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
            text: 'User has been registered successfully',
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('Create new task', callback_data: 'create_task'),
                    InlineKeyboardButton::make('Your tasks', callback_data: 'show_tasks')
                )
        );
        $this->end();
    }
}