<?php

namespace App\Repository;

use App\Entity\Capture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Capture>
 */
class CaptureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Capture::class);
    }

//    /**
//     * @return Capture[] Returns an array of Capture objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Capture
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * Récupère les captures des 7 derniers jours par type et salle
     */
    public function findLast7DaysByTypeAndRoom(int $typeId, int $roomId): array
    {
        $date7DaysAgo = new \DateTime('7 days ago');
        
        return $this->createQueryBuilder('c')
            ->where('c.room = :room')
            ->andWhere('c.type = :type')
            ->andWhere('c.dateCaptured >= :dateStart')
            ->setParameter('room', $roomId)
            ->setParameter('type', $typeId)
            ->setParameter('dateStart', $date7DaysAgo)
            ->orderBy('c.dateCaptured', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
