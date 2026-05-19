<?php
namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findNegativeCommentsNonOwner(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.sentiment = :neg')
            ->andWhere('c.isOwner = false')
            ->andWhere('c.score > :threshold')
            ->setParameter('neg', 'NEGATIVE')
            ->setParameter('threshold', 0.9)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getStatsByProduit(): array
    {
        return $this->createQueryBuilder('c')
            ->select('IDENTITY(c.produit) AS produitId, COUNT(c.id) AS nbCommentaires, AVG(c.score) AS moyenneScore')
            ->groupBy('c.produit')
            ->getQuery()
            ->getResult();
    }
}
