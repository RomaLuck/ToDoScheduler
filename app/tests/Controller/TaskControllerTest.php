<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TaskControllerTest extends WebTestCase
{
    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $userRepository = self::getContainer()->get(UserRepository::class);
        $this->testUser = $userRepository->findOneByEmail('test@gmail.com');
        $this->client->loginUser($this->testUser);
    }

    /**
     * @throws \Exception
     */
    public function testVisitingWhileLoggedIn(): void
    {
        $router = self::getContainer()->get('router');
        $this->client->request('GET', '/');

        self::assertResponseRedirects(
            rtrim($router->generate('app_task', [], UrlGeneratorInterface::ABSOLUTE_URL), '/'),
            301
        );
    }

    /**
     * @throws \Exception
     */
    public function testCreateTask(): void
    {
        $task = new Task();
        $task->setTitle('test')
            ->setUser($this->testUser)
            ->setDeadLine(DateTimeImmutable::createFromFormat('Y-m-d H:i', '2023-12-01 09:00'))
            ->setCreatedAt();
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->persist($task);
        $entityManager->flush();
        $tasks = self::getContainer()->get(TaskRepository::class)->findAll();

        self::assertContains('test', array_map(fn($row) => $row->getTitle(), $tasks));
    }
}
