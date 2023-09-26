<?php

namespace App\Telegram\Handlers;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

class DisplayTasksHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(Nutgram $bot): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['chat_id' => $bot->userId()]);
        $bot->sendMessage(
            text: "\xF0\x9F\x94\xA5 " . '<b>Your tasks: </b>',
            parse_mode: ParseMode::HTML,
        );
        $uncompletedTasks = $this->entityManager->getRepository(Task::class)->findUncompletedTasks($user);
        if (count($uncompletedTasks) > 0) {
            foreach ($uncompletedTasks as $uncompletedTask) {
                $bot->sendMessage(
                    text: "\xE2\x9E\xA1 " . $uncompletedTask->getTitle(),
                );
            }
        } else {
            $bot->sendMessage('You don\'t have any task');
        }
    }
}