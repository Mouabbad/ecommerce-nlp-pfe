<?php
namespace App\Controller\Vendor;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    #[Route('/vendor/statistics', name: 'vendor_statistics')]
    public function index(EntityManagerInterface $em): Response
    {
        $vendor = $this->getUser();

        $produits = $em->getRepository(Produit::class)->findBy(['vendor' => $vendor]);

        $stats = [];

        foreach ($produits as $produit) {
            $positiveCount = 0;
            $negativeCount = 0;

            $commentsArray = [];
            foreach ($produit->getComments() as $comment) {
                // عد التعليقات حسب المشاعر
                $sentiment = strtolower($comment->getSentiment());
                if ($sentiment === 'positive') {
                    $positiveCount++;
                } elseif ($sentiment === 'negative') {
                    $negativeCount++;
                }

                // تحضير التعليقات مع معلومات العميل
                $commentsArray[] = [
                    'client' => $comment->getUser()->getUsername(),
                    'commentaire' => $comment->getContent(),
                    'sentiment' => $sentiment,
                ];
            }

            $stats[] = [
                'produit' => $produit->getNom(),
                'positive' => $positiveCount,
                'negative' => $negativeCount,
                'comments' => $commentsArray,
            ];
        }

        return $this->render('vendor/statistics.html.twig', [
            'stats' => $stats,
        ]);
    }
}
