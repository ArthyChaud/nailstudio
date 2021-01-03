<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $page = $options['attr'][0];
        if ($page == 'accueil') {
            $builder->add('description', TextareaType::class)
                ->add('medias', FileType::class, [
                    'mapped' => false,
                    'required' => false,
                    'multiple' => true
                ])
                ->add('submit', SubmitType::class);
        } else if ($page == 'assos') {
            $builder->add('description', HiddenType::class)
                ->add('medias', FileType::class, [
                    'mapped' => false,
                    'required' => false,
                    'multiple' => true
                ])
                ->add('submit', SubmitType::class);
        } else if ($page == 'event') {
            $builder->add('medias', FileType::class, [
                    'mapped' => false,
                    'required' => false,
                    'multiple' => true
                ])
                ->add('description', HiddenType::class)
                ->add('submit', SubmitType::class);
        } else if ($page == 'formation') {
            $builder->add('description', TextareaType::class, array(
                    'attr' => array('rows' => '15')
                ))
                ->add('medias', FileType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => true
            ])
                ->add('submit', SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}
