<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCoursController extends AbstractController
{
    #[Route('/admin/cours', name: 'admin_cours_index')]
    public function index(CoursRepository $repo, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $cours = $repo->findBySearch($searchData);
            
            return $this->render('admin/cours/index.html.twig', [
                'form' => $form->createView(),
                'courses' => $cours
            ]);
        }

        return $this->render('admin/cours/index.html.twig', [
            'form' => $form->createView(),
            'courses' => $repo->findCours($request->query->getInt('page', 1))
        ]);
    }

    #[Route('admin/cours/{slug}/edit', name: 'admin_cours_edit')]
    public function edit(Cours $cours, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger)
    {
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $coverImage = $form->get('coverImage')->getData();
            if ($coverImage) {
                $originalFilename = pathinfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImage->guessExtension();
            }
            if ($coverImage) {
                $coverImage->move(
                    $this->getParameter('image_directory'),
                    $newFilename
                );
            }
            $cours->setMimeType($coverImage->getClientMimeType());
            $cours->setCoverImage($newFilename);
            
            foreach ($cours->getModules() as $i => $module) {
                $contenu = $form->get('modules')[$i]->get('contenu')->getData();
                if ($contenu) {
                    $originalFile = pathinfo($contenu->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFile = $slugger->slug($originalFile);
                    $newFile = $safeFile.'-'.uniqid().'.'.$contenu->guessExtension();
                }
                if ($contenu) {
                    $contenu->move(
                        $this->getParameter('module_directory'),
                        $newFile
                    );
                }
                $module->setMimeType($contenu->getClientMimeType());
                $module->setContenu($newFile);

                $module->setCours($cours);
                $module->setApproved(true);
                $manager->persist($module);
            }

            foreach ($cours->getCriteres() as $criteres) {
                $criteres->setCours($cours);
                $manager->persist($criteres);
            }

            $cours->setApproved(true);
            $manager->persist($cours);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications du cours <strong>{$cours->getTitre()}</strong> ont bien été enregistrées !"
            );

            return $this->redirectToRoute('admin_cours_index', [
                'slug' => $cours->getSlug()
            ]);
        }

        return $this->render('admin/cours/edit.html.twig', [
            'form' => $form->createView(),
            'cours' => $cours
        ]);
    }

    #[Route('admin/cours/{slug}/delete', name: 'admin_cours_delete')]
    public function delete(Cours $cours, EntityManagerInterface $manager)
    {
        $manager->remove($cours);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le cours <strong>{$cours->getTitre()}</strong> a bien été supprimé !"
        );

        return $this->redirectToRoute('admin_cours_index');
    }

    #[Route('admin/cours/{slug}/approve', name: 'admin_cours_approve')]
    public function approve(Cours $cours, EntityManagerInterface $manager)
    {
        $cours->setApproved(true);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le cours <strong>{$cours->getTitre()}</strong> a bien été approuvé !"
        );

        return $this->redirectToRoute('admin_cours_index');
    }

    #[Route('admin/cours/{slug}/disapprove', name: 'admin_cours_disapprove')]
    public function disapprove(Cours $cours, EntityManagerInterface $manager)
    {
        $cours->setApproved(false);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le cours <strong>{$cours->getTitre()}</strong> a été désapprouvé !"
        );

        return $this->redirectToRoute('admin_cours_index');
    }

    #[Route('admin/cours/create', name: 'admin_cours_create')]
    public function create(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger)
    {
        $cours = new Cours();

        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $coverImage = $form->get('coverImage')->getData();
            if ($coverImage) {
                $originalFilename = pathinfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImage->guessExtension();
            }
            if ($coverImage) {
                $coverImage->move(
                    $this->getParameter('image_directory'),
                    $newFilename
                );
            }
            $cours->setMimeType($coverImage->getClientMimeType());
            $cours->setCoverImage($newFilename);

            foreach ($cours->getModules() as $i => $module) {
                $contenu = $form->get('modules')[$i]->get('contenu')->getData();
                if ($contenu) {
                    $originalFile = pathinfo($contenu->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFile = $slugger->slug($originalFile);
                    $newFile = $safeFile.'-'.uniqid().'.'.$contenu->guessExtension();
                }
                if ($contenu) {
                    $contenu->move(
                        $this->getParameter('module_directory'),
                        $newFile
                    );
                }
                $module->setMimeType($contenu->getClientMimeType());
                $module->setContenu($newFile);

                $module->setCours($cours);
                $module->setApproved(true);
                $manager->persist($module);
            }

            foreach ($cours->getCriteres() as $criteres) {
                $criteres->setCours($cours);
                $manager->persist($criteres);
            }

            $cours->setAuteur($this->getUser());

            $cours->setApproved(true);
            $manager->persist($cours);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le cours <strong>{$cours->getTitre()}</strong> a bien été créé !"
            );

            return $this->redirectToRoute('admin_cours_index');
        }

        return $this->render('admin/cours/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('admin/cours/{slug}', name: 'admin_cours_show')]
    public function show(Cours $cours)
    {
        return $this->render('admin/cours/showCours.html.twig', [
            'cours' => $cours
        ]);
    }
}
