<?php

namespace App\Form;

use App\Entity\Role;
use ApplicationType;
use App\Entity\Utilisateur;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminUtilisateurAjoutType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, $this->getConfiguration('Nom', "Nom d'utilisateur"))
        ->add('prenom', TextType::class, $this->getConfiguration('Préom', "Prénom d'utilisateur"))
        ->add('email', TextType::class, $this->getConfiguration('Email', "Email d'utilisateur"))
        ->add('cin', TextType::class, $this->getConfiguration('CIN', "CIN d'utilisateur"))
        ->add('biographie', TextareaType::class, $this->getConfiguration("Biographie","Un aperçu du parcours professionnel/académique"))
        ->add('password', PasswordType::class, $this->getConfiguration('Mot de passe', "Mot de passe d'utilisateur"))
        ->add('passwordConfirm', PasswordType::class, $this->getConfiguration('Confimation de mot de passe', "Confirmer le mot de passe d'utilisateur"))
        ->add('avatar', FileType::class, [
            'label' => 'Photo de profil',
            'mapped' => false,
            'attr' => [
            'accept'=> 'image/*'
            ]
        ])
        ->add('UtilisateurRoles', EntityType::class,[
            'class' => Role::class,
            'label'=> 'Role',
            'choice_label' => 'titre',
            'multiple' => true,
            'expanded' => true,
            'choice_attr' => function($role, $key, $value) {
                return ['data-id' => $role->getId()];
            }
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
