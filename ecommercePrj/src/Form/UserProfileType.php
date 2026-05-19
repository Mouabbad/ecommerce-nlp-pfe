<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('firstName', TextType::class, ['required' => false])
            ->add('lastName', TextType::class, ['required' => false])
            ->add('phone', TextType::class, ['required' => false])
            ->add('address', TextType::class, ['required' => false])
            ->add('city', TextType::class, ['required' => false])
            ->add('plainPassword', RepeatedType::class, [
                  'type' => PasswordType::class,
                   'mapped' => false,
                   'required' => false,
                   'first_options'  => ['label' => 'Nouveau mot de passe'],
                    'second_options' => ['label' => 'Confirmer le mot de passe'],
                      'invalid_message' => 'Les mots de passe ne correspondent pas.',
                 ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
