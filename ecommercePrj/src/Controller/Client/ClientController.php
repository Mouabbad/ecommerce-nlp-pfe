<?php

namespace App\Controller\Client;

use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\SecurityBundle\Security;



class ClientController extends AbstractController
{
    //récupère les produits filtrés
    #[Route('/client/dashboard', name: 'client_dashboard')]
    public function dashboard(Request $request, ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
    {
        $search = $request->query->get('search', '');
        $categoryId = $request->query->get('category', '');

        $categories = $categorieRepository->findAll();

        // Méthode personnalisée à créer dans ProduitRepository
        $produits = $produitRepository->findBySearchAndCategory($search, $categoryId);

        return $this->render('client/dashboard.html.twig', [
            'products' => $produits,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $categoryId,
        ]);
    }
    




//la route responsables pour la redirection vers le profile(afiche les infos du client)
#[Route('/client/profile/view', name: 'client_profile_view')]
public function profileView(Security $security): Response
{
    $user = $security->getUser();

    return $this->render('client/profile_view.html.twig', [
        'user' => $user,
    ]);
}

}
