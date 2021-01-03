<?php

namespace App\Form;

use App\Entity\CarouselLike;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarouselLikeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $page = $options['attr'][0];
        if ($page == 'accueil') {
            $builder->add('name')
                ->add('description', TextareaType::class, array(
                    'attr' => array('rows' => '5')
                ))
                ->add('submit', SubmitType::class);
        } else if ($page == 'assos') {
            $builder->add('name')
                ->add('description', TextareaType::class, array(
                    'attr' => array('rows' => '15')
                ))
                ->add('submit', SubmitType::class);
        } else if ($page == 'event') {
            $builder->add('name')
                ->add('description', TextareaType::class, array(
                    'attr' => array('rows' => '15')
                ))
                ->add('submit', SubmitType::class);
        } else if ($page == 'formation') {
            $builder->add('name')
                ->add('description', TextareaType::class, array(
                    'attr' => array('rows' => '15')
                ))
                ->add('submit', SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CarouselLike::class,
        ]);
    }
}
