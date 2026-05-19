<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class VendorRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, ['label' => 'Prénom'])
            ->add('lastName', null, ['label' => 'Nom'])
            ->add('username', null, ['label' => "Nom d'utilisateur"])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'invalid_message' => 'Les adresses email ne correspondent pas.',
                'required' => true,
                'first_options'  => [
                    'label' => 'Email',
                    'constraints' => [
                        new NotBlank(['message' => 'Email requis.']),
                        new Email(['message' => 'Format d\'email invalide.']),
                    ]
                ],
                'second_options' => ['label' => 'Confirmer l\'email'],
            ])
            ->add('phone', null, ['label' => 'Téléphone'])
            ->add('city', null, ['label' => 'Ville'])
            ->add('address', null, ['label' => 'Adresse'])
            ->add('companyName', null, ['label' => 'Nom de l\'entreprise'])
            ->add('rib', null, [
                    'label' => 'RIB bancaire',
                    'constraints' => [
                        new NotBlank(['message' => 'Le RIB est requis.']),
                        new Length([
                          'min' => 16,
                          'max' => 16,
                        'exactMessage' => 'Le RIB doit contenir exactement 16 caractères.',
                         ]),
                    ],
                ])

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        ]),
                    ],
                ],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'mapped' => false,
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
