<?php

namespace App\Form;

use ApplicationType;
use App\Entity\Utilisateur;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, $this->getConfiguration("Nom", "Votre nom"))
            ->add('prenom', TextType::class, $this->getConfiguration("Prénom", "Votre prénom"))
            ->add('cin', IntegerType::class, $this->getConfiguration("CIN","Votre CIN"))
            ->add('email', EmailType::class, $this->getConfiguration("Email","Votre adresse email"))
            ->add('biographie', TextareaType::class, $this->getConfiguration("Biographie","Un aperçu de votre parcours professionnel/académique"))
            ->add('password', PasswordType::class, $this->getConfiguration("Mot de passe","Votre mot de passe"))
            ->add('passwordConfirm', PasswordType::class, $this->getConfiguration("Confirmation de mot de passe","Veuillez confirmer votre mot de passe"))
            ->add('avatar', FileType::class, [
                'label' => 'Photo de profil',
                'attr' => [
                'accept'=> 'image/*'
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
