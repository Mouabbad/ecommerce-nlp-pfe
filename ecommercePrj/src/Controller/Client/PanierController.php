<?php

namespace App\Controller\Client;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Repository\PanierRepository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;









final class PanierController extends AbstractController
{
//ajouter des produits dans le paniers 
#[Route('/panier/add/{id}', name: 'panier_add')]
public function add(Produit $produit, EntityManagerInterface $em, Security $security): Response
{
    $user = $security->getUser();
    

    // Vérifie si ce produit est déjà dans le panier du client
    $existing = $em->getRepository(Panier::class)->findOneBy([
        'user' => $user,
        'produit' => $produit,
    ]);

    if ($existing) {
        $existing->setQuantite($existing->getQuantite() + 1);
    } else {
        $panier = new Panier();
        $panier->setUser($user);
        $panier->setProduit($produit);
        $panier->setQuantite(1);
        $em->persist($panier);
    }

    $em->flush();

    $this->addFlash('success', 'Produit ajouté au panier.');
    return $this->redirectToRoute('client_dashboard');
}


//affichier la table des produits ajouter avec la quantite de chaque produit
   #[Route('/panier', name: 'panier_index')]
public function index(PanierRepository $repo, Security $security): Response
{
    $user = $security->getUser();
    $paniers = $repo->findBy(['user' => $user]);

    return $this->render('panier/index.html.twig', [
        'paniers' => $paniers,
    ]);
}
//supprimer un pranduit apartir du panier
#[Route('/panier/delete/{id}', name: 'panier_delete')]
public function delete(Panier $panier, EntityManagerInterface $em): Response
{
    $em->remove($panier);
    $em->flush();

    return $this->redirectToRoute('panier_index');
}

//modifier la quantite d'un produit
/*#[Route('/panier/update/{id}', name: 'panier_update', methods: ['POST'])]
public function update(Request $request, Panier $panier, EntityManagerInterface $em): Response
{
    $newQty = $request->request->getInt('quantite');
    if ($newQty > 0) {
        $panier->setQuantite($newQty);
        $em->flush();
    }

    return $this->redirectToRoute('panier_index');
}
*/

//Crée une méthode dédiée pour gérer l'incrémentation ou la décrémentation de la quantité via GET 

#[Route('/panier/update-quantity/{id}/{action}', name: 'panier_update_quantity')]
public function updateQuantity(Panier $panier, string $action, EntityManagerInterface $em): Response
{
    $quantity = $panier->getQuantite();

    if ($action === 'increase') {
        $panier->setQuantite($quantity + 1);
    } elseif ($action === 'decrease' && $quantity > 1) {
        $panier->setQuantite($quantity - 1);
    }

    $em->flush();

    return $this->redirectToRoute('panier_index');
}

}
