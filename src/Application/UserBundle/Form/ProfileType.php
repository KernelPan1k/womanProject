<?php

namespace Application\UserBundle\Form;

use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\Form\Type\ProfileFormType;
use Project\FrontBundle\Entity\Category;
use Project\FrontBundle\Entity\City;
use Project\FrontBundle\Entity\Context;
use Project\FrontBundle\Form\Select2\AjaxChoiceType;
use Project\FrontBundle\Form\Select2\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('current_password');
        $builder->remove('username');
        $builder->add('firstName', TextType::class, ['label' => 'Prénom']);
        $builder->add('lastName', TextType::class, ['label' => 'Nom de famille']);
        $builder->add(
            'dateOfBirth',
            DateType::class,
            [
                'label'  => 'Date de naissance',
                'widget' => 'single_text',
                'html5'  => false,
            ]
        );
        $builder->add(
            'city',
            AjaxChoiceType::class,
            [
                'view_class'   => City::class,
                'route_name'   => 'form_city_query',
                'choice_label' => 'name',
                'attr'         => [
                    'class' => 'col-md-12 col-sm-12 col-xs-12',
                ],
            ]
        );
        $builder->add(
            'categorys',
            Select2EntityType::class,
            [
                'class'         => Category::class,
                'multiple'      => true,
                'choice_label'  => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('c')
                        ->join('c.contexts', 't')
                        ->where('t.context IN(:context)')
                        ->setParameter('context', Context::CONTEXT_USER);
                },
            ]
        );
        $builder->add(
            'description',
            TextareaType::class,
            [
                'label'    => 'Description',
                'required' => false,
            ]
        );
    }

    public function getParent()
    {
        return ProfileFormType::class;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'required' => true,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'entreamies_user_profile';
    }
}
