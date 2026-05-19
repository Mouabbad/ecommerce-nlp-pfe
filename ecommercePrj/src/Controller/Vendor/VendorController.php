<?php

namespace App\Controller\Vendor;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class VendorController extends AbstractController
{
    #[Route('/vendor/dashboard', name: 'vendor_dashboard')]
    public function dashboard(EntityManagerInterface $em, Security $security): Response
    {
        // ناخد المستخدم الحالي (البائع)
        $vendor = $security->getUser();

        // نجيب المنتجات اللي عند هاد البائع
        $produits = $em->getRepository(Produit::class)->findBy(['vendor' => $vendor]);

        // نرجع القالب مع المتغير produits
        return $this->render('vendor/dashboard.html.twig', [
            'produits' => $produits,
        ]);
    }
}
