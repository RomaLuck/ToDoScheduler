<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findUncompletedTasks(UserInterface $user): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :f')
            ->setParameter('f', false)
            ->andWhere('t.user = :u')
            ->setParameter('u', $user)
            ->getQuery()
            ->getResult();
    }

    public function findCompleted(UserInterface $user): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :t')
            ->setParameter('t', true)
            ->andWhere('t.user = :u')
            ->setParameter('u', $user)
            ->getQuery()
            ->getResult();
    }

    public function findWithDeadLine(UserInterface $user): array
    {
        $qb = $this->createQueryBuilder('t');
        return $qb
            ->where($qb->expr()->isNotNull('t.deadLine'))
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Task[] Returns an array of Task objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
