<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
// * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function findAll($orderedBy  = 'id')
    {
        return $this->findBy(array(), array($orderedBy => 'DESC'));
    }

    /**
     * Check la disponnibilité d'une chambre avant de valider un booking
     * @param Booking $booking
     * @return bool
     */
    public function checkRoomIsAvailable(Booking $booking)
    {
        $startDate = $booking->getStartDate()->format('Y-m-d');
        $endDate = $booking->getEndDate()->format('Y-m-d');
        $queryBuiler = $this->createQueryBuilder('bk')
            ->innerJoin('bk.room', 'rm')
            ->andWhere('rm.id = :roomId')
            ->setParameter('roomId', $booking->getRoom()->getId())
            ->andWhere("(bk.startDate between :startdDate and :endDate or bk.endDate between :startdDate and :endDate)")
            ->setParameter('startdDate', $startDate)
            ->setParameter('endDate', $endDate)
        ;
//        dump($result);
//        dd($queryBuiler->getQuery()->getSQL());
        $result = $queryBuiler->getQuery()->getResult();
        return count($result) == 0;
    }

    // /**
    //  * @return Booking[] Returns an array of Booking objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
