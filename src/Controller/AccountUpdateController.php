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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountUpdateController extends AbstractController
{
    #[IsGranted('ROLE_APPRENANT')]
    #[Route('/compte/profile', name: 'compte_edit')]
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
            return $this->redirectToRoute('mon_compte',
            [
                'id' => $utilisateur->getId()
            ]);
        }

        return $this->render('compte/modifierprofile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_APPRENANT')]
    #[Route('/compte/password-update', name: 'password_edit')]
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
                return $this->redirectToRoute('mon_compte',
                [
                    'id' => $utilisateur->getId()
                ]);
            }
        }

        return $this->render('compte/password.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    #[Route('/compte/{id}', name: 'compte')]
    public function Account(Utilisateur $utilisateur)
    {
        return $this->render('user/profile.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/mon_compte/{id}', name: 'mon_compte')]
    public function myAccount(Utilisateur $utilisateur)
    {
        return $this->render('user/mon_compte.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/compte/{id}/mesCours', name: 'mes_cours')]
    public function myCourses(Utilisateur $utilisateur)
    {
        return $this->render('user/cours.html.twig', [
            'payments' => $utilisateur->getPayments(),
        ]);
    }
}
