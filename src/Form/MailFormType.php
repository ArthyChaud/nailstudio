<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true
            ])
            ->add('surname', TextType::class, [
                'label' => 'Prénom',
                'required' => true
            ])
            ->add('tel', TelType::class, [
                'label' => 'Téléphone',
                'required' => true
            ])
            ->add('mail', EmailType::class, [
                'label' => 'Email',
                'required' => true
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'required' => true
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Texte',
                'required' => true,
                'attr' => array('cols' => 30, 'rows' => 12)
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Envoyer"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
