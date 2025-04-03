<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Email'
                    ]
                ]
            )
            ->add('password',
                RepeatedType::class,
                [
                    'label' => false,
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'Mot de passe'
                        ]
                    ],
                    'second_options' => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'Confirmer le mot de passe'
                        ]
                    ]

                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Envoyer',
                    'attr' => [
                        'class' => 'bg-teal-600 hover:bg-teal-500 text-emerald-200 w-full'
                    ]
                ]
            )
        ;
    }
}