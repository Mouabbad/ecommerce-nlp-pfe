<?php
namespace App\Controller\Client;

use App\Entity\Panier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;   // <---- هذا مهم
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientOrderController extends AbstractController
{
    #[Route('/commande/checkout', name: 'client_order_checkout')]
    public function checkout(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $panierItems = $em->getRepository(Panier::class)->findBy(['user' => $user]);

        $cartItems = [];
        $total = 0;

        foreach ($panierItems as $item) {
            $produit = $item->getProduit();
            $quantite = $item->getQuantite();
            $prix = $produit->getPrix();

            $subtotal = $prix * $quantite;
            $total += $subtotal;

            $cartItems[] = [
                'nom' => $produit->getNom(),
                'quantite' => $quantite,
                'prix' => $prix,
                'subtotal' => $subtotal,
            ];
        }

        return $this->render('client/order/checkout.html.twig', [
            'user' => $user,
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    #[Route('/commande/confirm', name: 'client_order_confirm', methods: ['POST'])]
    public function confirm(EntityManagerInterface $em, Request $request): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // هنا تضيف منطق حفظ الطلب

        $this->addFlash('success', 'Commande confirmée avec succès !');

        return $this->redirectToRoute('client_dashboard');
    }
}
