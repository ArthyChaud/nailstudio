<?php

namespace App\Repository;

use App\Entity\CarouselLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CarouselLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarouselLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarouselLike[]    findAll()
 * @method CarouselLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarouselLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarouselLike::class);
    }

    // /**
    //  * @return CarouselLike[] Returns an array of CarouselLike objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CarouselLike
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
