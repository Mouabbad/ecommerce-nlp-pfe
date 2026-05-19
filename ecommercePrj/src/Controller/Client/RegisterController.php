<?php

namespace App\Controller\Client;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ClientRegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;



class RegisterController extends AbstractController
{
    #[Route('/register/client', name: 'client_register')]
public function register(
    Request $request,
    UserPasswordHasherInterface $passwordHasher,
    EntityManagerInterface $em,
    MailerInterface $mailer
): Response {
    $user = new User();
    $form = $this->createForm(ClientRegistrationFormType::class, $user);
    $form->handleRequest($request);



    if ($form->isSubmitted() && $form->isValid()) {
        // hash password
       $plainPassword = $form->get('plainPassword')->get('first')->getData();

    
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);


        // créer token
        $user->setToken(bin2hex(random_bytes(32)));
        $user->setIsVerified(false);

        $em->persist($user);
        $em->flush();

        // envoyer email de vérification
        $email = (new Email())
        ->from('monprojetshop@gmail.com')
        ->to($user->getEmail())
        ->subject('Confirmation de votre compte')
        ->html("<p>Bonjour, veuillez cliquer sur ce lien pour confirmer votre compte :</p>
                    <a href='http://127.0.0.1:8000/verify/{$user->getToken()}'>Activer mon compte</a>");

        $mailer->send($email);
        

        $this->addFlash('success', 'Un email de confirmation vous a été envoyé.');
        return $this->redirectToRoute('app_login');
    }

    return $this->render('client/register.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
}

    #[Route('/verify/{token}', name: 'client_verify')]
public function verify(string $token, EntityManagerInterface $em): Response
{
    $user = $em->getRepository(User::class)->findOneBy(['token' => $token]);

    if (!$user) {
        $this->addFlash('danger', 'Lien de vérification invalide.');
        return $this->redirectToRoute('app_login');
    }

    $user->setIsVerified(true);
    $user->setToken(null); // une fois utilisé, on supprime le token
    $em->flush();

    $this->addFlash('success', 'Votre compte est maintenant activé !');
    return $this->redirectToRoute('app_login');
}

}
