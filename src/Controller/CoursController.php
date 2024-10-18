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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CoursController extends AbstractController
{
    #[Route('/cours', name: 'cours_index')]
    public function index(Request $request, CoursRepository $repo): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $cours = $repo->findBySearch($searchData);
            
            return $this->render('cours/index.html.twig', [
                'form' => $form->createView(),
                'courses' => $cours
            ]);
        }

        return $this->render('cours/index.html.twig', [
            'form' => $form->createView(),
            'courses' => $repo->findCours($request->query->getInt('page', 1))
        ]);
    }

    #[IsGranted('ROLE_FORMATEUR')]
    #[Route('/cours/new', name: 'cours_create')]
    public function create(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger)
    {
        $cours = new Cours;

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
                $module->setApproved(false);
                $manager->persist($module);
            }

            foreach ($cours->getCriteres() as $criteres) {
                $criteres->setCours($cours);
                $manager->persist($criteres);
            }

            $cours->setAuteur($this->getUser());
            $cours->setApproved(false);

            $manager->persist($cours);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le cours <strong>{$cours->getTitre()}</strong> a bien été enregistré !"
            );

            return $this->redirectToRoute('cours_show', [
                'slug' => $cours->getSlug()
            ]);
        }

        return $this->render('cours/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Security('is_granted("ROLE_FORMATEUR") and user === cours.getAuteur()')]
    #[Route('/cours/{slug}/edit', name: 'cours_edit')]
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
                $module->setApproved(false);
                $manager->persist($module);
            }

            foreach ($cours->getCriteres() as $criteres) {
                $criteres->setCours($cours);
                $manager->persist($criteres);
            }

            $cours->setApproved(false);
            $manager->persist($cours);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications du cours <strong>{$cours->getTitre()}</strong> ont bien été enregistrées !"
            );

            return $this->redirectToRoute('cours_show', [
                'slug' => $cours->getSlug()
            ]);
        }

        return $this->render('cours/edit.html.twig', [
            'form' => $form->createView(),
            'cours' => $cours
        ]);
    }

    #[Route('/cours/{slug}', name: 'cours_show')]
    public function show(Cours $cours) {
        return $this->render('cours/show.html.twig', [
            'cours' => $cours
        ]);
    }

    #[Security('is_granted("ROLE_FORMATEUR") and user === cours.getAuteur()')]
    #[Route('/cours/{slug}/delete', name: 'cours_delete')]
    public function delete(Cours $cours, EntityManagerInterface $manager) {
        $manager->remove($cours);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le cours <strong>{$cours->getTitre()}</strong> a été supprimé !"
        );

        return $this->redirectToRoute('cours_index');
    }
}
