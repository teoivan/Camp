<?php

namespace App\Repository;

use App\Entity\ExerciseLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExerciseLog>
 */
class ExerciseLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExerciseLog::class);
    }

    //    /**
    //     * @return ExerciseLog[] Returns an array of ExerciseLog objects
    //     */
        public function findByWorkoutId($id): array
        {
            return $this->createQueryBuilder('e')
                ->andWhere('e.workout = :id')
                ->setParameter('id', $id)
                ->orderBy('e.workout', 'ASC')
                ->getQuery()
                ->getResult()
            ;
        }

    //    public function findOneBySomeField($value): ?ExerciseLog
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
