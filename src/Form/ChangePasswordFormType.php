<?php

namespace App\Form;

use ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordFormType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('password', PasswordType::class, $this->getConfiguration("Nouveau mot de passe","Votre nouveau mot de passe"))
        ->add('passwordConfirm', PasswordType::class, $this->getConfiguration("Confirmation de mot de passe","Veuillez confirmer votre mot de passe"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
