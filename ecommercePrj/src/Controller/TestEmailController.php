<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TestEmailController extends AbstractController
{
    #[Route('/test/email')]
public function test(MailerInterface $mailer): Response
{
    $email = (new Email())
        ->from('ayamouabbadaya@gmail.com')
        ->to('mouabbadmarwa@gmail.com')
        ->subject('Test Gmail depuis Symfony')
        ->text('Ça marche 👌');

    $mailer->send($email);

    return new Response('✔️ Email envoyé avec succès');
}

}
