<?php

namespace App\Form;

use App\Entity\SecretSanta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecretSantaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'label',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Titre de votre secret santa.',
                    ]
                ]
            )
            ->add(
                'registerMe',
                CheckboxType::class,
                [
                    'label' => 'S\'inscrire',
                    'row_attr' => [
                        'class' => 'flex flex-row-reverse'
                    ],
                    'mapped' => false,
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SecretSanta::class,
        ]);
    }
}