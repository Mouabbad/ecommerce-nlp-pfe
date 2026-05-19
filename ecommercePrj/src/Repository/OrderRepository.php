<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findByVendor($vendor)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.vendor = :vendor')
            ->setParameter('vendor', $vendor)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
