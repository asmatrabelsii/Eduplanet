<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizDoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['questions'] as $question) {
            $builder->add((string) $question->getId(), ChoiceType::class, [
                'label' => $question->getEnonce(),
                'choices' => $this->shuffleChoices([
                    $question->getChoix1(),
                    $question->getChoix2(),
                    $question->getChoix3(),
                    $question->getChoix4(),
                ]),
                'expanded' => true,
                'multiple' => false,
            ]);
        }
    }
    
    private function shuffleChoices(array $choices): array
    {
        shuffle($choices);
        $shuffledChoices = [];
        foreach ($choices as $choice) {
            $shuffledChoices[$choice] = $choice;
        }
        return $shuffledChoices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('questions');
        $resolver->setAllowedTypes('questions', 'iterable');
    }
}
