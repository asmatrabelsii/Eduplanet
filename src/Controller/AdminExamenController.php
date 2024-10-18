<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Examen;
use App\Form\ExamenType;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\ExamenRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminExamenController extends AbstractController
{
    #[Route('/admin/cours/{slug}/examen', name: 'admin_examen_index')]
    public function index(ExamenRepository $repo, Cours $cours): Response
    {
        $examen = $repo->find($cours->getExamen()->getId());
        return $this->render('admin/examen/index.html.twig', [
            'examen' => $examen
        ]);
    }

    #[Route('/admin/cours/{slug}/examen/new', name: 'admin_examen_new')]
    public function new(Cours $cours, Request $request, EntityManagerInterface $manager): Response
    {
        $examen = new Examen();
        $form = $this->createForm(ExamenType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $questions = $form->get('questions')->getData();
            foreach ($questions as $question) {
                $examen->addQuestion($question);
                $question->setExamen($examen);
                $manager->persist($question);
            }

            $examen->setPrix($form->get('prix')->getData());
            $examen->setBareme($form->get('bareme')->getData());
            $examen->setCours($cours);
            $examen->setApproved(true);
            $manager->persist($examen);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'examen a bien été créé !"
            );

            return $this->redirectToRoute('admin_cours_show', [
                'slug' => $cours->getSlug(),
            ]);
        }
        return $this->render('admin/examen/new.html.twig', [
            'form' => $form->createView(),
            'slug' => $cours->getSlug(),
        ]);
    }

    #[Route('/admin/cours/{slug}/examen/{id}/edit', name: 'admin_examen_edit')]
    public function edit(Examen $examen, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ExamenType::class, $examen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $questions = $form->get('questions')->getData();
            foreach ($questions as $question) {
                $examen->addQuestion($question);
                $question->setExamen($examen);
                $manager->persist($question);
            }

            $examen->setPrix($form->get('prix')->getData());
            $examen->setBareme($form->get('bareme')->getData());
            $examen->setApproved(true);
            $manager->persist($examen);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'examen a bien été modifié !"
            );

            return $this->redirectToRoute('admin_cours_show', [
                'slug' => $examen->getCours()->getSlug(),
            ]);
        }
        return $this->render('admin/examen/edit.html.twig', [
            'form' => $form->createView(),
            'slug' => $examen->getCours()->getSlug(),
        ]);
    }

    #[Route('/admin/cours/{slug}/examen/{id}/delete', name: 'admin_examen_delete')]
    public function delete(Examen $examen, EntityManagerInterface $manager): Response
    {
        $manager->remove($examen);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'examen a bien été supprimé !"
        );

        return $this->redirectToRoute('admin_cours_show', [
            'slug' => $examen->getCours()->getSlug(),
        ]);
    }

    #[Route('/admin/examen/{id}/approve', name: 'admin_examen_approve')]
    public function approve(Examen $examen, EntityManagerInterface $manager): Response
    {
        $examen->setApproved(true);
        $manager->persist($examen);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'examen a bien été approuvé !"
        );

        return $this->redirectToRoute('admin_examen_index', [
            'slug' => $examen->getCours()->getSlug(),
        ]);
    }

    #[Route('/admin/examen/{id}/disapprove', name: 'admin_examen_disapprove')]
    public function disapprove(Examen $examen, EntityManagerInterface $manager): Response
    {
        $examen->setApproved(false);
        $manager->persist($examen);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'examen a été désapprouvé !"
        );

        return $this->redirectToRoute('admin_examen_index', [
            'slug' => $examen->getCours()->getSlug(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/certification', name: 'admin_certification_index')]
    public function index_examen(SessionRepository $repo, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $certifications = $repo->findBySearch($searchData);
            
            return $this->render('admin/certification/index.html.twig', [
                'form' => $form->createView(),
                'certifications' => $certifications
            ]);
        }
        return $this->render('admin/certification/index.html.twig', [
            'form' => $form->createView(),
            'certifications' => $repo->findCertifications($request->query->getInt('page', 1))
        ]);
    }
}
