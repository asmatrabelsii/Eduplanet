<?php

namespace App\Controller;

use App\Entity\Cathegories;
use App\Form\AdminCathegoriesType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CathegoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCathegoriesController extends AbstractController
{
    #[Route('/admin/categories', name: 'admin_cathegories_index')]
    public function index(CathegoriesRepository $repo): Response
    {
        return $this->render('admin/cathegories/index.html.twig', [
            'categories' => $repo->findAll(),
        ]);
    }

    #[Route('/admin/categories/{id}/edit', name: 'admin_cathegories_edit')]
    public function edit(Cathegories $cathegorie, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdminCathegoriesType::class, $cathegorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($cathegorie);
            $manager->flush();
            
            $this->addFlash(
                'success',
                "La catégorie <strong>{$cathegorie->getLibelle()}</strong> a bien été modifiée !"
            );

            return $this->redirectToRoute('admin_cathegories_index');
        }

        return $this->render('admin/cathegories/edit.html.twig', [
            'categorie' => $cathegorie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/categories/{id}/delete', name: 'admin_cathegories_delete')]
    public function delete(Cathegories $cathegorie, EntityManagerInterface $manager): Response
    {
        $manager->remove($cathegorie);
        $manager->flush();

        $this->addFlash(
            'success',
            "La catégorie <strong>{$cathegorie->getLibelle()}</strong> a bien été supprimée !"
        );

        return $this->redirectToRoute('admin_cathegories_index');
    }

    #[Route('/admin/categories/new', name: 'admin_cathegories_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $cathegorie = new Cathegories();

        $form = $this->createForm(AdminCathegoriesType::class, $cathegorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($cathegorie);
            $manager->flush();

            $this->addFlash(
                'success',
                "La catégorie <strong>{$cathegorie->getLibelle()}</strong> a bien été ajoutée !"
            );

            return $this->redirectToRoute('admin_cathegories_index');
        }

        return $this->render('admin/cathegories/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
