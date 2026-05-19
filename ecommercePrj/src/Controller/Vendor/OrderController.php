<?php

namespace App\Controller\Vendor;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/vendor/orders', name: 'vendor_orders_')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function listOrders(Security $security, EntityManagerInterface $em): Response
    {
        $vendor = $security->getUser();

        $orders = $em->getRepository(Order::class)->findBy(['vendeur' => $vendor]);

        return $this->render('vendor/orders/list.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function editOrder(Order $order, Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $vendor = $security->getUser();

        if ($order->getVendeur() !== $vendor) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette commande.');
        }

        if ($request->isMethod('POST')) {
            $newStatus = $request->request->get('statut');
            if (in_array($newStatus, ['en cours', 'livré', 'annulé'])) {
                $order->setStatut($newStatus);
                $em->flush();

                $this->addFlash('success', 'Statut de la commande mis à jour');
                return $this->redirectToRoute('vendor_orders_list');
            } else {
                $this->addFlash('error', 'Statut invalide');
            }
        }

        return $this->render('vendor/orders/edit.html.twig', [
            'order' => $order,
        ]);
    }

    

   
}
