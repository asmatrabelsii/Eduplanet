<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Panier;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Entity\Utilisateur;
use App\Form\AdminUtilisateurType;
use App\Repository\RoleRepository;
use App\Entity\ResetPasswordRequest;
use App\Form\AdminUtilisateurAjoutType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUtilisateurController extends AbstractController
{
    #[Route('/admin/utilisateurs', name: 'admin_utilisateur_index')]
    public function index(UtilisateurRepository $repo, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $utilisateurs = $repo->findBySearch($searchData);
            
            return $this->render('admin/utilisateur/index.html.twig', [
                'form' => $form->createView(),
                'utilisateurs' => $utilisateurs
            ]);
        }

        return $this->render('admin/utilisateur/index.html.twig', [
            'form' => $form->createView(),
            'utilisateurs' => $repo->findUtilisateurs($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/admin/utilisateurs/{id}/delete', name: 'admin_utilisateur_delete')]
    public function delete(Utilisateur $utilisateur, EntityManagerInterface $manager): Response
    {
        $resetPasswordRequests = $manager->getRepository(ResetPasswordRequest::class)->findBy(['user' => $utilisateur]);

        foreach ($resetPasswordRequests as $resetPasswordRequest) {
            $manager->remove($resetPasswordRequest);
        }

        $cours = $manager->getRepository(Cours::class)->findBy(['auteur' => $utilisateur]);

        foreach ($cours as $coursEntity) {
            $manager->remove($coursEntity);
        }

        $manager->remove($utilisateur);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le compte de <strong>{$utilisateur->getFullName()}</strong> a été supprimé !"
        );

        return $this->redirectToRoute('admin_utilisateur_index');
    }

    #[Route('/admin/utilisateurs/{id}/edit', name: 'admin_utilisateur_edit')]
    public function edit(Utilisateur $utilisateur, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger, RoleRepository $repo)
    {
        $form = $this->createForm(AdminUtilisateurType::class, $utilisateur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatar = $form->get('avatar')->getData();
            if ($avatar) {
                $originalFilename = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$avatar->guessExtension();
            }
            if ($avatar) {
                $avatar->move(
                    $this->getParameter('avatar_directory'),
                    $newFilename
                );
            }

            $utilisateur->setAvatar($newFilename);
            $utilisateur->setIsVerified(true);

            $selectedRoles = $form->get('UtilisateurRoles')->getData();
            $roles = $repo->findAll();

            foreach ($selectedRoles as $roleselected) {
                foreach ($roles as $role) { 
                    if ($role != $roleselected) {
                        $utilisateur->removeUtilisateurRole($role);
                        $role->removeUtilisateur($utilisateur);
                        $manager->persist($role);
                    }
                }
            }
            
            foreach ($selectedRoles as $role) {
                $utilisateur->addUtilisateurRole($role);
                $role->addUtilisateur($utilisateur);
                $manager->persist($role);
            }
            
            $manager->persist($utilisateur);
            $manager->flush();
            
            $this->addFlash(
                'success',
                "Le compte de <strong>{$utilisateur->getFullName()}</strong> a bien été modifié !"
            );

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        return $this->render('admin/utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/utilisateurs/{id}/show', name: 'admin_utilisateur_show')]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('admin/utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/admin/utilisateurs/{id}/verify', name: 'admin_utilisateur_verify')]
    public function verify(Utilisateur $utilisateur, EntityManagerInterface $manager): Response
    {
        $utilisateur->setIsVerified(true);
        $manager->persist($utilisateur);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le compte de <strong>{$utilisateur->getFullName()}</strong> a bien été activé !"
        );

        return $this->redirectToRoute('admin_utilisateur_index');
    }

    #[Route('/admin/utilisateurs/{id}/unverify', name: 'admin_utilisateur_unverify')]
    public function unverify(Utilisateur $utilisateur, EntityManagerInterface $manager): Response
    {
        $utilisateur->setIsVerified(false);
        $manager->persist($utilisateur);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le compte de <strong>{$utilisateur->getFullName()}</strong> a été désactivé !"
        );

        return $this->redirectToRoute('admin_utilisateur_index');
    }

    #[Route('/admin/utilisateurs/new', name: 'admin_utilisateur_new')]
    public function new(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger, UserPasswordHasherInterface $userPasswordHasher)
    {
        $utilisateur = new Utilisateur();
        $panier = new Panier();

        $form = $this->createForm(AdminUtilisateurAjoutType::class, $utilisateur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatar = $form->get('avatar')->getData();
            if ($avatar) {
                $originalFilename = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$avatar->guessExtension();
            }
            if ($avatar) {
                $avatar->move(
                    $this->getParameter('avatar_directory'),
                    $newFilename
                );
            }

            $utilisateur->setAvatar($newFilename);
            $utilisateur->setIsVerified(true);

            $utilisateur->setPassword(
                $userPasswordHasher->hashPassword(
                    $utilisateur,
                    $form->get('password')->getData()
                )
            );

            $selectedRoles = $form->get('UtilisateurRoles')->getData();            
            foreach ($selectedRoles as $role) {
                $utilisateur->addUtilisateurRole($role);
                $role->addUtilisateur($utilisateur);
                $manager->persist($role);
            }

            $utilisateur->setPanier($panier);
            $panier->setOwner($utilisateur);
            $manager->persist($panier);
            $manager->flush();
            
            $manager->persist($utilisateur);
            $manager->flush();
            $this->redirectToRoute('admin_utilisateur_index');

            $this->addFlash(
                'success',
                "Le compte de <strong>{$utilisateur->getFullName()}</strong> a bien été ajouté !"
            );

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        return $this->render('admin/utilisateur/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}  
