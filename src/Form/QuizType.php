<?php

namespace App\Form;

use App\Entity\Quiz;
use App\Form\QuizQuestionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quizQuestions', CollectionType::class,
            [
                'entry_type' => QuizQuestionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Count([
                        'min' => 3,
                        'max' => 10,
                        'minMessage' => 'Il faut ajouter au moins 3 questions',
                        'maxMessage' => 'Vous ne pouvez pas ajouter plus de 10 questions',
                    ]),
                ],
            ]
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
