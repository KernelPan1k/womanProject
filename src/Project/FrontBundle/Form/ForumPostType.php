<?php

namespace Project\FrontBundle\Form;

use Project\FrontBundle\Form\ProtoForm\AbstractForumPostType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ForumPostType
 * @package Project\FrontBundle\Form
 */
class ForumPostType extends AbstractForumPostType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add(
            'submit',
            SubmitType::class,
            ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-success']]
        );
    }
}
