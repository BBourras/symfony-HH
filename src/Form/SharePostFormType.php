<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SharePostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recipient_email', EmailType::class, [
                'label' => 'Email du destinataire',
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ],
                ])
            ->add('sender_name', TextType::class, [
                'label' => 'Mon nom',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2])
                ],
            ])
            ->add('sender_email', EmailType::class, [
                'label' => 'Mon email',
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ],
            ])
            ->add('sender_comments', TextareaType::class, [
                'label' => 'Mon message',
                'help' => 'Contenu optionnel'
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
