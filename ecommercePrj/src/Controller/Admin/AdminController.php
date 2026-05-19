<?php
namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserProfileType;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(
        UserRepository $userRepository,
        ProduitRepository $produitRepository,
        CommentRepository $commentRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $clients = array_filter($userRepository->findAll(), fn($u) => in_array('ROLE_CLIENT', $u->getRoles()));
        $vendeurs = array_filter($userRepository->findAll(), fn($u) => in_array('ROLE_VENDOR', $u->getRoles()));

        $vendeursData = [];
        foreach ($vendeurs as $vendeur) {
            $produits = $produitRepository->findBy(['vendor' => $vendeur]);
            $produitsStats = [];

            foreach ($produits as $produit) {
                $comments = $commentRepository->findBy(['produit' => $produit]);
                $avgScore = null;
                if (count($comments) > 0) {
                    $total = 0;
                    foreach ($comments as $c) {
                        $total += $c->getScore() ?? 0;
                    }
                    $avgScore = $total / count($comments);
                }
                $produitsStats[] = [
                    'produit' => $produit,
                    'averageScore' => $avgScore,
                    'commentsCount' => count($comments)
                ];
            }
            $vendeursData[] = [
                'vendeur' => $vendeur,
                'produitsStats' => $produitsStats,
            ];
        }

        $negativeComments = $commentRepository->findNegativeCommentsNonOwner();

        return $this->render('admin/dashboard.html.twig', [
            'clients' => $clients,
            'vendeursData' => $vendeursData,
            'negativeComments' => $negativeComments,
        ]);
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $users = $userRepository->findAll();

        return $this->render('admin/users.html.twig', ['users' => $users]);
    }

    #[Route('/admin/user/add', name: 'admin_user_add')]
    public function chooseAddUser(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/user_add_choice.html.twig');
    }

    #[Route('/admin/user/add/client', name: 'admin_user_add_client')]
    public function addClient(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $user->setRoles(['ROLE_CLIENT']);

        $form = $this->createForm(UserProfileType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Client ajouté avec succès.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter un Client',
        ]);
    }

    #[Route('/admin/user/add/vendor', name: 'admin_user_add_vendor')]
    public function addVendor(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $user->setRoles(['ROLE_VENDOR']);

        $form = $this->createForm(UserProfileType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Vendeur ajouté avec succès.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter un Vendeur',
        ]);
    }

    #[Route('/admin/user/edit/{id}', name: 'admin_user_edit')]
    public function editUser(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
            $em->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier utilisateur',
        ]);
    }

    #[Route('/admin/user/delete/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Utilisateur supprimé.');
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/admin/commentaire/supprimer/{id}', name: 'admin_comment_delete', methods: ['POST'])]
    public function deleteComment(int $id, CommentRepository $commentRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $comment = $commentRepository->find($id);
        if (!$comment) {
            $this->addFlash('error', 'Commentaire introuvable.');
            return $this->redirectToRoute('admin_dashboard');
        }

        if ($comment->getSentiment() === 'NEGATIVE' && !$comment->getIsOwner()) {
            $em->remove($comment);
            $em->flush();
            $this->addFlash('success', 'Commentaire supprimé.');
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce commentaire.');
        }

        return $this->redirectToRoute('admin_dashboard');
    }
}
