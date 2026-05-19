<?php

namespace App\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Service\SentimentAnalyzer;  // <=== ضيف الservice

final class CommentController extends AbstractController
{
    private $sentimentAnalyzer;

    public function __construct(SentimentAnalyzer $sentimentAnalyzer)
    {
        $this->sentimentAnalyzer = $sentimentAnalyzer;
    }

    #[Route('/client/produit/{id}/comment', name: 'client_add_comment', methods: ['GET', 'POST'])]
    public function addComment(Request $request, EntityManagerInterface $em, Security $security, Produit $produit): Response
    {
        $comment = new Comment();
        $user = $security->getUser();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($user);
            $comment->setProduit($produit);
            $comment->setCreatedAt(new \DateTimeImmutable());

            // استعمل ال service لتحليل النص
            $analysis = $this->sentimentAnalyzer->analyzeSentiment($comment->getContent());
            if ($analysis !== null) {
                $comment->setSentiment($analysis['sentiment'] ?? null);
                // إذا عندك score فـ الكومنت حطها، وإلا ماعندكش ماشي ضروري
                if (method_exists($comment, 'setScore') && isset($analysis['score'])) {
                    $comment->setScore($analysis['score']);
                }
            }

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès !');

            return $this->redirectToRoute('client_dashboard');
        }

        return $this->render('client/add_comment.html.twig', [
            'commentForm' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    #[Route('/comment/edit/{id}', name: 'comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        if ($comment->getUser() !== $user) {
            throw $this->createAccessDeniedException('vous ne pouvez pas modifier ce commentaire');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // تحليل النص قبل الحفظ
            $analysis = $this->sentimentAnalyzer->analyzeSentiment($comment->getContent());
            if ($analysis !== null) {
                $comment->setSentiment($analysis['sentiment'] ?? null);
                if (method_exists($comment, 'setScore') && isset($analysis['score'])) {
                    $comment->setScore($analysis['score']);
                }
            }

            $em->flush();

            $this->addFlash('success', 'la modification à été fait avec succès.');
            return $this->redirectToRoute('client_dashboard'); 
        }

        return $this->render('comment/edit.html.twig', [
            'commentForm' => $form->createView(),
        ]);
    }

   
//la ssupression du commentaire par le clienr qui le ecrit
#[Route('/comment/delete/{id}', name: 'comment_delete', methods: ['POST'])]
public function delete(Request $request, Comment $comment, EntityManagerInterface $em, Security $security): Response
{
    $user = $security->getUser();

    // le client ne peut supprimer que som commentaire
    if ($comment->getUser() !== $user) {
        throw $this->createAccessDeniedException('vous ne pouvez pas supprimer ce commentaire');
    }

    if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
        $em->remove($comment);
        $em->flush();
        $this->addFlash('success', 'la suppression à été fait avec succès.');
    }

    return $this->redirectToRoute('client_dashboard'); 
}




}
