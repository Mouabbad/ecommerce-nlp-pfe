<?php

namespace App\Controller\Vendor;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/vendor/produits', name: 'vendor_produit_')]
class ProductController extends AbstractController
{
    private $uploadsDirectory;
    private $slugger;

    public function __construct(string $uploadsDirectory, SluggerInterface $slugger)
    {
        $this->uploadsDirectory = $uploadsDirectory;
        $this->slugger = $slugger;
    }

    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(EntityManagerInterface $em, Security $security): Response
    {
        $vendor = $security->getUser();

        $produits = $em->getRepository(Produit::class)->findBy(['vendor' => $vendor]);

        return $this->render('vendor_dashboard', [
            'produits' => $produits,
        ]);
    }

    #[Route('/ajouter', name: 'add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $vendor = $security->getUser();
        $produit = new Produit();
        $produit->setVendor($vendor);
        $produit->setCreatedAt(new \DateTimeImmutable());  // <-- تعيين الوقت الحالي

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->uploadsDirectory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // إدارة الخطأ هنا، مثلا: $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }

                $produit->setImage($newFilename);
            }

            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès');

            return $this->redirectToRoute('vendor_dashboard');
        }

        return $this->render('vendor/produit/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Produit $produit, Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $vendor = $security->getUser();

        if ($produit->getVendor() !== $vendor) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce produit.');
        }

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->uploadsDirectory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // إدارة الخطأ هنا
                }

                $produit->setImage($newFilename);
            }

            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès');

            return $this->redirectToRoute('vendor_dashboard');
        }

        return $this->render('vendor/produit/edit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $em, Security $security): Response
    {
        $vendor = $security->getUser();

        if ($produit->getVendor() !== $vendor) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce produit.');
        }

        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $em->remove($produit);
            $em->flush();

            $this->addFlash('success', 'Produit supprimé avec succès');
        }

        return $this->redirectToRoute('vendor_dashboard');
    }

    #[Route('/home', name: 'page_home')]
public function home(): Response
{
    $produits = $this->getDoctrine()->getRepository(Produit::class)->findAll();

    return $this->render('home.html.twig', [
        'produits' => $produits
    ]);
}

}
