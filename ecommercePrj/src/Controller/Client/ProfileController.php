<?php

namespace App\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserProfileType;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    //por la route du page de modification d'infos du client
    #[Route('/client/profile', name: 'client_profile')]
   

public function editProfile(Request $request, Security $security, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
{
    $user = $security->getUser();

    $form = $this->createForm(UserProfileType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $plainPassword = $form->get('plainPassword')->getData();

        if ($plainPassword) {
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }

        $em->flush();

        $this->addFlash('success', 'Profil mis à jour avec succès !');

        return $this->redirectToRoute('client_profile');
    }

    return $this->render('client/profile.html.twig', [
        'profileForm' => $form->createView(),
    ]);
}

}
