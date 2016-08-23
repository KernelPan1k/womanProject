<?php

namespace Project\FrontBundle\Form\ProtoForm;

use Project\FrontBundle\Entity\Forum;
use Project\FrontBundle\Entity\ForumPost;
use Project\FrontBundle\Form\Select2\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ForumPostType
 * @package Project\FrontBundle\Form
 */
abstract class AbstractForumPostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, ['label' => 'Titre']);
        $builder->add('description', TextareaType::class, ['label' => 'Message']);
        $builder->add(
            'forum',
            Select2EntityType::class,
            [
                'class'        => Forum::class,
                'choice_label' => 'name',
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ForumPost::class,
            ]
        );
    }
}
