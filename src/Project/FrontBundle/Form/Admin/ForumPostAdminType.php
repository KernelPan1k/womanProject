<?php

namespace Project\FrontBundle\Form\Admin;

use Application\UserBundle\Entity\User;
use Project\FrontBundle\Form\ProtoForm\AbstractForumPostType;
use Project\FrontBundle\Form\Select2\AjaxChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ForumPostType
 * @package Project\FrontBundle\Form
 */
class ForumPostAdminType extends AbstractForumPostType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add(
            'user',
            AjaxChoiceType::class,
            [
                'view_class'   => User::class,
                'route_name'   => 'form_user_query',
                'choice_label' => function (User $user) {
                    return $user->getId().' '.$user->getUsername();
                },
                'attr'         => [
                    'class' => 'col-md-12 col-sm-12 col-xs-12',
                ],
            ]
        );
        $builder->add(
            'submit',
            SubmitType::class,
            ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-success']]
        );
    }
}
