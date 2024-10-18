<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdminUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,
                [
                    'label' => 'Nom'
                ])
            ->add('prenom', TextType::class
                ,[
                    'label' => 'Prénom'
                ])
            ->add('email', TextType::class,
                [
                    'label' => 'Email'
                ])
            ->add('cin', TextType::class,
                [
                    'label' => 'CIN'
                ])
            ->add('biographie', TextareaType::class,
                [
                    'label' => 'Biographie'
                ])
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
