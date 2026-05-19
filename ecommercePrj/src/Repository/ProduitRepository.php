<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    //pour rechercher a un produit a partir de son nom ou sa categorie
    public function findBySearchAndCategory(string $search = '', $categoryId = null)
{
    $qb = $this->createQueryBuilder('p')
        ->leftJoin('p.categorie', 'c')
        ->addSelect('c');

    if ($search !== '') {
        $qb->andWhere('p.nom LIKE :search')
            ->setParameter('search', '%'.$search.'%');
    }

    if ($categoryId) {
        $qb->andWhere('c.id = :categoryId')
            ->setParameter('categoryId', $categoryId);
    }

    return $qb->getQuery()->getResult();
}


    //    /**
    //     * @return Produit[] Returns an array of Produit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
