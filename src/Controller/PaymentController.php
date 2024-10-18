<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\PaymentExamenRepository;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[IsGranted('ROLE_APPRENANT')]
    #[Route (path:'/cours/{slug}/learn', name:'cours_read'), ]
    public function learn(Cours $cours, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Commentaire();

        $form = $this->createForm(CommentaireType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCours($cours)
                    ->setAuteur($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', 'Votre commentaire a bien été ajouté');

            return $this->redirectToRoute('cours_read', ['slug' => $cours->getSlug()]);
        }

        return $this->render('user/learn.html.twig',[
            'cours' => $cours,
            'form'  => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_APPRENANT')]
    #[Route (path:'/cours/{slug}/learn/comment/{id}', name:'comment_edit'), ]
    public function editComment(Commentaire $comment, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(CommentaireType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', 'Votre commentaire a bien été modifié');

            return $this->redirectToRoute('cours_read', ['slug' => $comment->getCours()->getSlug()]);
        }

        return $this->render('user/editComment.html.twig',[
            'comment' => $comment,
            'form'  => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_APPRENANT')]
    #[Route (path:'/cours/{slug}/learn/comment/{id}/delete', name:'comment_delete'), ]
    public function deleteComment(Commentaire $comment, EntityManagerInterface $manager)
    {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash('success', 'Votre commentaire a été supprimé');

        return $this->redirectToRoute('cours_read', ['slug' => $comment->getCours()->getSlug()]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/payments', name: 'admin_payment_index')]
    public function index(PaymentRepository $repo, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $payments = $repo->findBySearch($searchData);
            
            return $this->render('admin/payment/index.html.twig', [
                'form' => $form->createView(),
                'payments' => $payments
            ]);
        }
        return $this->render('admin/payment/index.html.twig', [
            'form' => $form->createView(),
            'payments' => $repo->findPayments($request->query->getInt('page', 1))
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/payments/examen', name: 'admin_payment_examen_index')]
    public function index_examen(PaymentExamenRepository $repo, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $payments = $repo->findBySearch($searchData);
            
            return $this->render('admin/payment/index_examen.html.twig', [
                'form' => $form->createView(),
                'payments' => $payments
            ]);
        }
        return $this->render('admin/payment/index_examen.html.twig', [
            'form' => $form->createView(),
            'payments' => $repo->findPayments($request->query->getInt('page', 1))
        ]);
    }
}
