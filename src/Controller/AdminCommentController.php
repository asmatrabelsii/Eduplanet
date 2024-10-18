<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Model\SearchData;
use App\Entity\Commentaire;
use App\Form\AdminCommentType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    #[Route('/admin/comments', name: 'admin_comment_index')]
    public function index(CommentaireRepository $repo, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $comments = $repo->findBySearch($searchData);
            
            return $this->render('admin/comment/index.html.twig', [
                'form' => $form->createView(),
                'comments' => $comments
            ]);
        }
        return $this->render('admin/comment/index.html.twig', [
            'form' => $form->createView(),
            'comments' => $repo->findComments($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/admin/comments/{id}/delete', name: 'admin_comment_delete')]
    public function delete(Commentaire $comment, EntityManagerInterface $manager): Response
    {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire de <strong>{$comment->getAuteur()->getFullName()}</strong> a bien été supprimé !"
        );

        return $this->redirectToRoute('admin_comment_index');
    }

    #[Route('/admin/comments/{id}/edit', name: 'admin_comment_edit')]
    public function edit(Commentaire $comment, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();
            
            $this->addFlash(
                'success',
                "Le commentaire de <strong>{$comment->getAuteur()->getFullName()}</strong> a bien été modifié !"
            );

            return $this->redirectToRoute('admin_comment_index');
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }
}
