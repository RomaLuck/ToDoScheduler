<?php

namespace App\Telegram\Handlers;

use App\Repository\TaskRepository;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

class DisplayTasksHandler
{
    public function __construct(private readonly TaskRepository $repository)
    {
    }

    public function __invoke(Nutgram $bot): ?Message
    {
        $message = 'Your tasks: ' . PHP_EOL;
        foreach ($this->repository->findUncompletedTasks() as $uncompletedTask) {
            $message .= ' - ' . $uncompletedTask->getTitle() . PHP_EOL;
        }
        return $bot->sendMessage($message);
    }
}