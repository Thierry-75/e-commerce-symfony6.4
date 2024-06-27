<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
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
            ->add('lastname', TextType::class, [
                'attr' => ['class' => 'form-control'], 'label' => 'Nom', 'label_attr' => ['class' => 'form-label'],
                'constraints' => [
                    new Sequentially([
                        new NotBlank(message: 'Please enter your lastname'),
                        new Length(['min' => 2, 'max' => 30, 'minMessage' => 'min 2 characters', 'maxMessage' => 'max 30 characters']),
                        new Regex(
                            pattern: '/^[a-zA-Z-\' éèàïç]{2,30}$/i',
                            htmlPattern: '^[a-zA-Z-\' éèàïç]{2,30}$'
                        )
                    ])
                ]
            ])
            ->add('firstname', TextType::class, [
                'attr' => ['class' => 'form-control'], 'label' => 'Prénom', 'label_attr' => ['class' => 'form-label mt-2'],
                'constraints' => [
                    new Sequentially([
                        new NotBlank(message: 'Please enter your firstname'),
                        new Length(['min' => 2, 'max' => 30, 'minMessage' => 'min 2 characters', 'maxMessage' => 'max 30 characters']),
                        new Regex(
                            pattern: '/^[a-zA-Z-\' éèàïç]{2,30}$/i',
                            htmlPattern: '^[a-zA-Z-\' éèàïç]{2,30}$'
                        )
                    ])
                ]
            ])
            ->add('address', TextType::class, [
                'attr' => ['class' => 'form-control '], 'label' => 'Adresse', 'label_attr' => ['class' => 'form-label mt-2'],
                'constraints' => [
                    new Sequentially([
                        new NotBlank(message: 'Please enter your address'),
                        new Length(['min' => 5, 'max' => 50, 'minMessage' => 'min 5 characters', 'maxMessage' => 'max 50 characters']),
                        new Regex(
                            pattern: '/^[a-zA-Z0-9-\' éèàïç]{5,50}$/i',
                            htmlPattern: '^[a-zA-Z0-9-\' éèàïç]{5,50}$'
                        )
                    ])
                ]
            ])
            ->add('zipcode', TextType::class, [
                'attr' => ['class' => 'form-control '], 'label' => 'Code postal', 'label_attr' => ['class' => 'form-label mt-2'],
                'constraints' => [
                    new Sequentially([
                        new NotBlank(message: 'Please enter your zip code'),
                        new Length(['min' => 5, 'max' => 5, 'minMessage' => 'min 5 characters', 'maxMessage' => 'max 5 characters']),
                        new Regex(
                            pattern: '/^((2[A|B])|[0-9]{2})[0-9]{3}$/i',
                            htmlPattern: '^((2[A|B])|[0-9]{2})[0-9]{3}$'
                        )
                    ])
                ]
            ])
            ->add('city', TextType::class, [
                'attr' => ['class' => 'form-control '], 'label' => 'Ville', 'label_attr' => ['class' => 'form-label mt-2'],
                'constraints' => [
                    new Sequentially([
                        new NotBlank(message: 'Please enter your city'),
                        new Length(['min' => 2, 'max' => 25, 'minMessage' => 'min 2 characters', 'maxMessage' => 'max 25 characters']),
                        new Regex(
                            pattern: '/^[a-zA-Z-\' éèàïç]{2,25}$/i',
                            htmlPattern: '^[a-zA-Z-\' éèàïç]{2,25}$'
                        )
                    ])
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => [ 'class'=>'form-control ',
                    'autocomplete' => 'new-password',
                    'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'AZaz09#?!@$ %^&*- authorized 10 to 12 characters'
                ],
                'constraints' => [
                    new Sequentially([
                        new NotBlank(['message' => 'Please enter your password']),
                        new Length([
                            'min' => 10, 'max' => 12, 'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'maxMessage' => 'Your password should be max {{ limit }} characters'
                        ]),
                        new Regex(
                            pattern: '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$ %^&*-]).{10,12}$/i',
                            htmlPattern: '^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$ %^&*-]).{10,12}$'
                        )
                    ])
                ],
            ])
            ->add('Rgpd', CheckboxType::class, [
                'attr'=>['class'=>'form-check mt-4'], 'label' => ' J\'accepte les conditions générales','label_attr' => ['class' => 'form-label mt-4'],
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->addDate(...));
            ;
    }

    public function addDate(PostSubmitEvent $event)
    {
        $data = $event->getData();
        if(!($data instanceof Users))return;
        $data->setCreatedAt(new \DateTimeImmutable());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
