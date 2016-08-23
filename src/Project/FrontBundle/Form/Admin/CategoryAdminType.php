<?php

namespace Project\FrontBundle\Form\Admin;

use Project\FrontBundle\Entity\Category;
use Project\FrontBundle\Entity\Context;
use Project\FrontBundle\Form\Select2\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom '])
            ->add(
                'contexts',
                Select2EntityType::class,
                [
                    'class'        => Context::class,
                    'choice_label' => 'context',
                    'multiple'     => true,
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-success']]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Category::class,
                'required'   => true,
            ]
        );
    }
}
