<?php

namespace App\Form;

use App\Services\UserRequirements\UserRequirementsEnumFlag;
use App\Services\UserRequirements\UserRequirementsHandler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserRequirementsType extends AbstractType
{
    public function __construct(
        private UserRequirementsHandler $userRequirementsHandler,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $requirements = $this->userRequirementsHandler->handle();

        if ($requirements->has(UserRequirementsEnumFlag::PSEUDO)) {
            $builder->add(
                'pseudo',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Pseudo',
                    ]
                ]
            );
        }

        if ($requirements->has(UserRequirementsEnumFlag::PASSWORD)) {
            $builder->add(
                'password',
                PasswordType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Mot de passe',
                    ]
                ]
            );
        }
    }
}
