<?php

namespace Project\FrontBundle\Form;

use Project\FrontBundle\Form\ProtoForm\AbstractPostResponseType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PostResponseType
 * @package Project\FrontBundle\Form
 */
class PostResponseType extends AbstractPostResponseType
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
