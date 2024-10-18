<?php

namespace App\Form;

use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'label' => 'Libellé du module',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Tapez le libellé du module'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du module',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Tapez une description'
                ]
            ])
            ->add('contenu', FileType::class,
                array('data_class' => null), [
                'label' => 'Contenu du module',
                'required' => true,
                'attr' => [
                    'accept'=> 'image/*, video/*, .pdf'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
        ]);
    }
}
