<?php

namespace App\Repository;

use App\Entity\Exercise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exercise>
 */
class ExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercise::class);
    }

    //    /**
    //     * @return Exercise[] Returns an array of Exercise objects
    //     */
        public function findExerciseById($id): Exercise
        {
            $resp= $this->createQueryBuilder('e')
                ->andWhere('e.id = :id')
                ->setParameter('id', $id)
                ->orderBy('e.id', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
            return $resp[0];
        }

    //    public function findOneBySomeField($value): ?Exercise
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
