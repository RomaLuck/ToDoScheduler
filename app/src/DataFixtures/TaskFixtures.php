<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture
{
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@gmail.com')
            ->setPassword('123456')
            ->setAcceptedTerms(true)
            ->setTimeZone('Europe/Warsaw');
        $manager->persist($user);
        $manager->flush();

        $task = new Task();
        $task->setTitle('test task')
            ->setUser($user)
            ->setCreatedAt()
            ->setDeadLine(DateTimeImmutable::createFromFormat('Y-m-d H:i', '2023-12-01 09:00'));
        $manager->persist($task);
        $manager->flush();
    }
}
