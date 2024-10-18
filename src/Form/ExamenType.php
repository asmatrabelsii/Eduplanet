<?php

namespace App\Form;

use App\Entity\Examen;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ExamenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix', NumberType::class,
                [
                    'label' => 'Prix de l\'examen',
                    'attr' => [
                        'placeholder' => 'Prix de l\'examen',
                        'class' => 'form-control',
                    ],
                ])
            ->add('bareme', NumberType::class,
                [
                    'label' => 'Barème de l\'examen',
                    'attr' => [
                        'placeholder' => 'Le pourcentage nécessaire pour réussir l\'examen',
                        'class' => 'form-control',
                    ],
                ])
            ->add('questions', CollectionType::class,
                [
                    'entry_type' => QuestionType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'constraints' => [
                        new Count([
                            'min' => 30,
                            'max' => 120,
                            'minMessage' => 'Il faut ajouter au moins 30 questions',
                            'maxMessage' => 'Vous ne pouvez pas ajouter plus de 120 questions',
                        ]),
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Examen::class,
        ]);
    }
}
