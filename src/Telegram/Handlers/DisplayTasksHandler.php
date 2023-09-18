<?php

namespace App\Telegram\Handlers;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

class DisplayTasksHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(Nutgram $bot): ?Message
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['chat_id' => $bot->userId()]);
        $message = 'Your tasks: ' . PHP_EOL;
        foreach ($this->entityManager->getRepository(Task::class)->findUncompletedTasks($user) as $uncompletedTask) {
            $message .= ' - ' . $uncompletedTask->getTitle() . PHP_EOL;
        }
        return $bot->sendMessage($message);
    }
}