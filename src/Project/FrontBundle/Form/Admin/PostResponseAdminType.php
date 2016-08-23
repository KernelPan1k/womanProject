<?php

namespace Project\FrontBundle\Form\Admin;

use Application\UserBundle\Entity\User;
use Project\FrontBundle\Entity\ForumPost;
use Project\FrontBundle\Entity\PostResponse;
use Project\FrontBundle\Form\ProtoForm\AbstractPostResponseType;
use Project\FrontBundle\Form\Select2\AjaxChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PostResponseType
 * @package Project\FrontBundle\Form
 */
class PostResponseAdminType extends AbstractPostResponseType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add(
            'post',
            AjaxChoiceType::class,
            [
                'label'        => "Le post concerné",
                'view_class'   => ForumPost::class,
                'route_name'   => 'form_forumpost_query',
                'choice_label' => function (ForumPost $post) {
                    return $post->getId().' '.$post->getUser()->getUsername().' '.substr(
                        $post->getTitle(),
                        0,
                        20
                    );
                },
                'attr'         => [
                    'class' => 'col-md-12 col-sm-12 col-xs-12',
                ],
            ]
        );
        $builder->add(
            'user',
            AjaxChoiceType::class,
            [
                'view_class'   => User::class,
                'label'        => "L'utilisateur",
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
            'parent',
            AjaxChoiceType::class,
            [
                'view_class'   => PostResponse::class,
                'label'        => 'Répondre à une réponse (commentaire)',
                'route_name'   => 'form_forumresponse_query',
                'choice_label' => function (PostResponse $reponse) {
                    return $reponse->getId().' '.$reponse->getUser()->getUsername().' '.substr(
                        $reponse->getMessage(),
                        0,
                        20
                    );
                },
                'attr'         => [
                    'class' => 'col-md-12 col-sm-12 col-xs-12',
                ],
            ]
        );
        $builder->add('like', IntegerType::class);

        $builder->add(
            'submit',
            SubmitType::class,
            ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-success']]
        );
    }
}
