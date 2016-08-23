<?php

namespace Project\FrontBundle\Form\Admin;

use Doctrine\ORM\EntityRepository;
use Project\FrontBundle\Entity\Category;
use Project\FrontBundle\Entity\Context;
use Project\FrontBundle\Entity\Forum;
use Project\FrontBundle\Form\Select2\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ForumType
 * @package Project\FrontBundle\Form
 */
class ForumAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['label' => 'Nom', 'required' => true]);
        $builder->add('file', FileType::class, ['label' => 'Image', 'required' => false]);
        $builder->add('alt', TextType::class, ['label' => 'Alt', 'required' => false]);
        $builder->add(
            'category',
            Select2EntityType::class,
            [
                'class'         => Category::class,
                'choice_label'  => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('c')
                        ->join('c.contexts', 't')
                        ->where('t.context IN(:context)')
                        ->setParameter('context', Context::CONTEXT_FORUM);
                },
            ]
        );
        $builder->add('submit', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-success']]);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Forum::class,
            ]
        );
    }
}
