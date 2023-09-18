<?php

namespace App\Telegram\Conversations;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

class CreateTaskConversation extends Conversation
{
    protected ?string $step = 'askTaskName';

    protected string $taskName;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function askTaskName(Nutgram $bot): void
    {
        $bot->sendMessage('What task do you want to create?');
        $this->next('recap');
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function recap(Nutgram $bot): void
    {
        $task = new Task();
        $title = trim(htmlspecialchars($bot->message()->text));
        if ($title === '') {
            $bot->sendMessage('Title is empty');
            $this->end();
        }
        $task->setTitle($title);
        $task->setUser($this->entityManager->getRepository(User::class)->findOneBy(['chat_id' => $bot->chatId()]));
        $task->setCreatedAt();
        $task->setStatus(false);
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        $bot->sendMessage('Task has been created');
        $this->end();
    }
}