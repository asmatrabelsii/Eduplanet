<?php

namespace App\Controller;

use App\Form\CompteType;
use App\Entity\Utilisateur;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminCompteController extends AbstractController
{
    #[Route('/admin/login', name: 'admin_compte_login')]
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('admin/compte/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    #[Route('/admin/mon_compte/{id}', name: 'admin_compte')]
    public function myAccount(Utilisateur $utilisateur)
    {
        return $this->render('admin/compte/mon_compte.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('admin/mon_compte/{id}/edit_account', name: 'compte_admin_edit')]
    public function ModifierProfile(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger)
    {
        $utilisateur = $this->getUser();

        $form = $this->createForm(CompteType::class, $utilisateur);
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

            $manager->persist($utilisateur);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été modifié !"
            );
            return $this->redirectToRoute('admin_compte',
            [
                'id'=>$utilisateur->getId()
            ]);
        }

        return $this->render('admin/compte/edit_account.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('admin/mon_compte/{id}/edit_password', name: 'password_admin_edit')]
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $encoder)
    {
        $utilisateur = $this->getUser();
        $passwordUpdate = new PasswordUpdate();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if (!password_verify($passwordUpdate->getOldPassword(), $utilisateur->getPassword())) {
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));
            } else {
                $password = $encoder->hashPassword($utilisateur, $passwordUpdate->getNewPassword());
                $utilisateur->setPassword($password);

                $manager->persist($utilisateur);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié !"
                );
                return $this->redirectToRoute('admin_compte',
                [
                    'id'=>$utilisateur->getId()
                ]);
            }
        }

        return $this->render('admin/compte/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
