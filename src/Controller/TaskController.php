<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('{_locale<%app.supported.locales%>}')]
class TaskController extends AbstractController
{
    #[Route('/', name: 'app_task', methods: ['GET'])]
    public function index(TaskRepository $taskRepository, Request $request): Response
    {
        $sort = $request->query->get('sortBy') ?? 'createdAt';
        $result = match ($request->query->get('filter')) {
            'Active' => $taskRepository->findUncompletedTasks($this->getUser()),
            'Completed' => $taskRepository->findCompleted($this->getUser()),
            'Has-due-date' => $taskRepository->findWithDeadLine($this->getUser()),
            default => $taskRepository->findBy(
                ['user' => $this->getUser()],
                ['status' => 'ASC', $sort => 'DESC']),
        };

        return $this->render('task/index.html.twig', [
            'tasks' => $result
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/create', name: 'create_task', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $title = trim(htmlspecialchars($request->request->get('title')));
        if (empty($title)) {
            $this->addFlash('danger', 'Task is empty');
            return $this->redirectToRoute('app_task');
        }

        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        if ($user === null) {
            $this->addFlash('danger', 'User is not authorized');
            return $this->redirectToRoute('app_task');
        }

        $deadline = $request->request->get('deadline');
        if ($deadline) {
            $task->setDeadLine(DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $deadline));
        }

        $task->setTitle($title);
        $task->setUser($user);
        $task->setStatus(false);
        $task->setCreatedAt();
        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('app_task');
    }

    #[Route('/switch_status/{id}', name: 'switch_status')]
    public function switchStatus(Task $task, EntityManagerInterface $entityManager): Response
    {
        $task->setStatus(!$task->isStatus());
        $entityManager->flush();

        return $this->redirectToRoute('app_task');
    }

    #[Route('/delete/{id}', name: 'delete_task')]
    public function delete(Task $task, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('app_task');
    }
}
