<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    public TaskRepository $repository;

    public EntityManagerInterface $entityManager;

    public ChatterInterface $chatter;

    public function __construct(TaskRepository $repository, EntityManagerInterface $entityManager, ChatterInterface $chatter)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->chatter = $chatter;
    }

    #[Route('/', name: 'app_task')]
    public function index(): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $this->repository->findBy([], ['status' => 'ASC', 'createdAt' => 'DESC']),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/create', name: 'create_task', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $task = new Task();
        $title = trim(htmlspecialchars($request->request->get('title')));
        if (empty($title)) {
            return $this->redirectToRoute('app_task');
        }
        $deadline = $request->request->get('deadline');
        if ($deadline) {
            $task->setDeadLine(DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $deadline));
        }
        $task->setTitle($title);
        $task->setCreatedAt();
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_task');
    }

    #[Route('/switch_status/{id}', name: 'switch_status')]
    public function switchStatus(Task $task): Response
    {
        $task->setStatus(!$task->isStatus());
        $this->entityManager->flush();
        return $this->redirectToRoute('app_task');
    }

    #[Route('/delete/{id}', name: 'delete_task')]
    public function delete(Task $task): Response
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_task');
    }

    /**
     * @throws Exception
     */
    public function sendTaskReminders(): ?string
    {
        $currentTime = (new DateTimeImmutable('now', new DateTimeZone('Europe/Warsaw')))
            ->format('Y-m-d H:i');

        $tasks = $this->repository->findUncompletedTasks();

        foreach ($tasks as $task) {
            if ($this->isReminderTime($task->getDeadLine(), $currentTime)) {
                $this->sendTaskReminder($task);
                $task->setStatus(true);
                $this->entityManager->flush();
                return $task->getTitle();
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
