<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Module;
use App\Form\ModuleType;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModuleController extends AbstractController
{
    #[Security('is_granted("ROLE_FORMATEUR") and user === module.getCours().getAuteur()')]
    #[Route('/cours/{slug}/module/{id}/edit', name: 'module_edit')]
    public function editModule(Module $module, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contenu = $form->get('contenu')->getData();
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
            $module->setApproved(false);
            $manager->persist($module);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le module <strong>{$module->getLibelle()}</strong> a bien été modifié !"
            );

            return $this->redirectToRoute('cours_show', [
                'slug' => $module->getCours()->getSlug(),
            ]);
        }
        return $this->render('module/edit.html.twig', [
            'form' => $form->createView(),
            'id' => $module->getId(),
            'slug' => $module->getCours()->getSlug(),
            'module' => $module,
        ]);
    }

    #[Security('is_granted("ROLE_FORMATEUR") and user === module.getCours().getAuteur()')]
    #[Route('/cours/{slug}/module/{id}/delete', name: 'module_delete')]
    public function deleteModule(Module $module, EntityManagerInterface $manager)
    {
        $manager->remove($module);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le module <strong>{$module->getLibelle()}</strong> a été supprimé !"
        );

        return $this->redirectToRoute('cours_show', [
            'id' => $module->getId(),
            'slug' =>$module->getCours()->getSlug(),
        ]);
    }

    #[Security('is_granted("ROLE_FORMATEUR") and user === cours.getAuteur()')]
    #[Route('/cours/{slug}/module/new', name: 'module_new')]
    public function newModule(Cours $cours, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        $module = new Module();
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contenu = $form->get('contenu')->getData();
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
            $manager->flush();

            $this->addFlash(
                'success',
                "Le module <strong>{$module->getLibelle()}</strong> a bien été ajouté !"
            );

            return $this->redirectToRoute('cours_show', [
                'slug' => $cours->getSlug(),
            ]);
        }
        return $this->render('module/new.html.twig', [
            'form' => $form->createView(),
            'slug' => $cours->getSlug(),
        ]);
    }

    #[Route('/cours/{slug}/module/index', name: 'module_index')]
    public function index(ModuleRepository $repo, Request $request, Cours $cours)
    {
        return $this->render('module/index.html.twig', [
            'modules' => $repo->findModule($request->query->getInt('page', 1),$cours)
        ]);
    }
}
