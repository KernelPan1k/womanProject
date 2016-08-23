<?php

namespace Project\FrontBundle\Form\Admin;

use Application\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Project\FrontBundle\Entity\Category;
use Project\FrontBundle\Entity\Context;
use Project\FrontBundle\Entity\News;
use Project\FrontBundle\Form\Select2\AjaxChoiceType;
use Project\FrontBundle\Form\Select2\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NewsAdminType
 * @package Project\FrontBundle\Form
 */
class NewsAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'Titre',
            ]
        );
        $builder->add(
            'teaser',
            TextareaType::class,
            [
                'required' => false,
                'label'    => 'Chapeau',
            ]
        );
        $builder->add(
            'content',
            CKEditorType::class,
            [
                'label'       => 'Contenu',
                'config_name' => 'default',
            ]
        );
        $builder->add(
            'file',
            FileType::class,
            [
                'label'    => 'Image',
                'required' => false,
            ]
        );
        $builder->add(
            'alt',
            TextType::class,
            [
                'label'    => 'Texte alternatif',
                'required' => false,
            ]
        );
        $builder->add(
            'category',
            Select2EntityType::class,
            [
                'class'         => Category::class,
                'choice_label'  => 'name',
                'multiple'      => false,
                'label'         => 'Catégorie',
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('c')
                        ->join('c.contexts', 't')
                        ->where('t.context IN(:context)')
                        ->setParameter('context', Context::CONTEXT_NEWS);
                },
            ]
        );
        $builder->add(
            'enabled',
            CheckboxType::class,
            [
                'required' => false,
                'label'    => 'Activer',
            ]
        );
        $builder->add(
            'author',
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

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $form    = $event->getForm();
                $enabled = $form->get('enabled')->getData();
                if (!$enabled) {
                    return true;
                }
                /** @var News $news */
                $news  = $form->getData();
                $valid =
                    null !== $news->getTitle() && !empty($news->getTitle())
                    &&
                    null !== $news->getContent() && !empty($news->getContent());

                if (!$valid) {
                    return $form->get('title')->addError(
                        new FormError("Vous ne pouvez pas publier l'actualité, elle n'est pas complète")
                    );
                }

                return null;
            }
        );
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'required'   => true,
                'data_class' => News::class,
            ]
        );
    }
}
