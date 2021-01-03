<?php

namespace App\Repository;

use App\Entity\CategoryAccounting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategoryAccounting|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryAccounting|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryAccounting[]    findAll()
 * @method CategoryAccounting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryAccountingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryAccounting::class);
    }

    // /**
    //  * @return CategoryAccounting[] Returns an array of CategoryAccounting objects
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
    public function findOneBySomeField($value): ?CategoryAccounting
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
