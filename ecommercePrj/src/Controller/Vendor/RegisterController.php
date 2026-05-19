<?php

namespace App\Controller\Vendor;

use App\Entity\User;
use App\Form\VendorRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RegisterController extends AbstractController
{
    #[Route('/register/vendor', name: 'vendor_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $user = new User();
        $form = $this->createForm(VendorRegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->get('first')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $user->setRoles(['ROLE_VENDOR']);
            $user->setIsVerified(false);
            $user->setToken(bin2hex(random_bytes(32)));

            $em->persist($user);
            $em->flush();

            $email = (new Email())
                 ->from('ayamouabbadaya@gmail.com')
                ->to($user->getEmail())
                ->subject('Confirmez votre inscription')
                ->html("<p>Bonjour, veuillez cliquer sur ce lien pour confirmer votre compte :</p>
                        <a href='http://127.0.0.1:8000/vendor/verify/{$user->getToken()}'>Activer mon compte</a>");

            $mailer->send($email);

            //$this->addFlash('success', 'Un email de confirmation vous a été envoyé.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('vendor/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/vendor/verify/{token}', name: 'vendor_verify')]
    public function verify(string $token, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Lien de vérification invalide.');
            return $this->redirectToRoute('app_login');
        }

        $user->setIsVerified(true);
        $user->setToken(null);
        $em->flush();

        $this->addFlash('success', 'Votre compte est maintenant activé !');
        return $this->redirectToRoute('app_login');
    }
}
