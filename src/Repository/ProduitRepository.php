<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }
    public function getBesoin()
    {
        $queryBuilder=$this->createQueryBuilder('p');
        $queryBuilder->select('p')
            ->from('Produit', 'p')
            ->where('p.besoin > ?1')
            ->orderBy('p.libelle', 'ASC')
            ->setParameter(1, 0);
 }
    /**
    /* @return Produit[] Returns an array of Produit objects
    */

    public function findByExampleField()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.besoin > ?1')
            ->setParameter('1', 0)
            ->orderBy('p.libelle', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }



    public function findOneBySomeField(): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.besoin > ?1')
            ->setParameter('1', 0)
            ->orderBy('p.libelle', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
