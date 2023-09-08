<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/', name: 'app_task')]
    public function index(TaskRepository $repository): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $repository->findBy([], ['status' => 'ASC', 'createdAt' => 'DESC']),
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
            return $this->redirectToRoute('app_task');
        }
        $deadline = $request->request->get('deadline');
        if ($deadline) {
            $task->setDeadLine(DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $deadline));
        }
        $task->setTitle($title);
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
