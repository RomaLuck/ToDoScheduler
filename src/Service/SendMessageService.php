<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

class SendMessageService
{
    private ChatterInterface $chatter;
    private TaskRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(ChatterInterface $chatter, TaskRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->chatter = $chatter;
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws Exception
     */
    public function sendTaskReminders(): ?string
    {
        $currentTime = (new DateTimeImmutable('now', new DateTimeZone('Europe/Warsaw')))
            ->format('Y-m-d H:i');

        $users = $this->entityManager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $tasks = $this->repository->findUncompletedTasks($user);
            foreach ($tasks as $task) {
                if ($this->isReminderTime($task->getDeadLine(), $currentTime)) {
                    $this->sendTaskReminder($task);
                    $task->setStatus(true);
                    $this->entityManager->flush();
                    return $task->getTitle();
                }
            }
        }
        return null;
    }

    private function isReminderTime(DateTimeImmutable $deadline, string $currentTime): bool
    {
        $reminderTimeAdjusted = $deadline->format('Y-m-d H:i');

        return $reminderTimeAdjusted < $currentTime;
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function sendTaskReminder(Task $task): void
    {
        $chatMessage = new ChatMessage($task->getTitle());
        $this->chatter->send($chatMessage);
    }
}