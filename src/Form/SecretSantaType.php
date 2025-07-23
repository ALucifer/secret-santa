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
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
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

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SecretSanta::class,
            'attr' => [
                'data-turbo-frame' => '_top'
            ]
        ]);
    }
}