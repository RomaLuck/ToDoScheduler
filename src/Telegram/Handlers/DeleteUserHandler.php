<?php

namespace App\Telegram\Handlers;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use SergiX44\Nutgram\Nutgram;

class DeleteUserHandler
{
    public function __construct(private readonly EntityManagerInterface $entityManager,)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Nutgram $bot): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['chat_id' => $bot->chatId()]);
        if ($user === null) {
            return;
        }
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $bot->sendMessage("User has been deleted successfully \xF0\x9F\x98\x9E");
    }
}