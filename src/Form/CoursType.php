<?php

namespace App\Form;

use ApplicationType;
use App\Entity\Cours;
use App\Form\ModuleType;
use App\Form\CriteresType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class CoursType extends ApplicationType
{ 
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, $this->getConfiguration('Titre du cours', 'Tapez le titre du cours'))
            ->add('description', TextareaType::class, $this->getConfiguration('Description du cours', 'Tapez une description'))
            ->add('prix', NumberType::class, $this->getConfiguration('Prix','Indiquez le prix de votre cours'))
            ->add('cathegorie')
            ->add('coverImage', FileType::class,
                array('data_class' => null), [
                'label' => 'Image/vidéo de couverture',
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'accept'=> 'image/*, video/*'
                ]
            ])
            ->add(
                'modules',
                CollectionType::class,
                [
                    'entry_type' =>ModuleType::class,
                    'allow_add' =>true,
                    'allow_delete' =>true,
                    'constraints' => [
                        new Count([
                            'min' => 1,
                            'minMessage' => 'Il faut ajouter au moins un module',
                        ]),
                    ],
                ]
            )
            ->add(
                'criteres',
                CollectionType::class,
                [
                    'entry_type' =>CriteresType::class,
                    'allow_add' =>true,
                    'allow_delete' =>true,
                    'constraints' => [
                        new Count([
                            'min' => 1,
                            'minMessage' => 'Il faut ajouter au moins un critère',
                        ]),
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}
