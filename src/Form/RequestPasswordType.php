<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Sequentially;

class RequestPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'attr' => ['class' => 'form-control mb-2 ', 'autofocus' => true], 'label' => 'Email','label_attr' => ['class' => 'form-label mt-2'],
            'constraints' => [
                new Sequentially([
                    new NotBlank(message: "Please enter your email"),
                    new Length(['max' => 180, 'maxMessage' => 'Your email should be at least {{ limit }} characters']),
                    new Email(message: 'The email {{ value }} is not a valid email.')
                ])
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
