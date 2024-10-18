<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enonce', TextareaType::class,
                [
                    'label' => 'Enoncé',
                    'attr' => [
                        'placeholder' => 'Enoncé',
                        'class' => 'form-control',
                    ],
                ])
            ->add('choix1', TextType::class,
                [
                    'label' => 'Choix 1',
                    'attr' => [
                        'placeholder' => 'Choix 1 (Reponse vraie)',
                        'class' => 'form-control',
                    ],
                ])
            ->add('choix2',TextType::class,
                [
                    'label' => 'Choix 2',
                    'attr' => [
                        'placeholder' => 'Choix 2',
                        'class' => 'form-control',
                    ],
                ])
            ->add('choix3', TextType::class,
                [
                    'label' => 'Choix 3',
                    'attr' => [
                        'placeholder' => 'Choix 3',
                        'class' => 'form-control',
                    ],
                ])
            ->add('choix4', TextType::class,
                [
                    'label' => 'Choix 4',
                    'attr' => [
                        'placeholder' => 'Choix 4',
                        'class' => 'form-control',
                    ],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
