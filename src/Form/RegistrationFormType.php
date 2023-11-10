<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'form.label.email',
                'attr' => [
                    'placeholder' => 'form.placeholder.email'
                ],
                'constraints' => [
                    new Email([
                        'message' => 'form.messages.email'
                    ])
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'border-2 border-primary mt-3',
                    'id' => 'checkTerms'
                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'form.messages.agree_terms',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'form.label.password',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'form.placeholder.password'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.messages.password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'form.messages.password_min',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'registration'
        ]);
    }
}
