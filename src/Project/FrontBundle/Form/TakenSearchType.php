<?php

namespace Project\FrontBundle\Form;

use Application\UserBundle\Entity\User;
use Project\FrontBundle\Entity\City;
use Project\FrontBundle\Entity\Region;
use Project\FrontBundle\Form\Select2\AjaxChoiceType;
use Project\FrontBundle\Form\Select2\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TakenSearchType extends AbstractType
{
    /**
     * @var TokenStorageInterface $tokenInterface
     */
    private $tokenInterface;

    /**
     * TakenSearchType constructor.
     *
     * @param TokenStorageInterface $tokenStorageInterface
     */
    public function __construct(TokenStorageInterface $tokenStorageInterface)
    {
        $this->tokenInterface = $tokenStorageInterface;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->tokenInterface->getToken()->getUser();
        $city = $options['city'];

        if (null === $user || !$user instanceof User) {
            $builder->add(
                'search',
                Select2EntityType::class,
                [
                    'class'        => Region::class,
                    'choice_label' => 'name',
                ]
            );
        } else {
            $builder->add(
                'search',
                AjaxChoiceType::class,
                [
                    'label'        => 'Ville',
                    'view_class'   => City::class,
                    'route_name'   => 'form_city_query',
                    'choice_label' => 'name',
                    'required'     => false,
                    'data'         => $city,
                ]
            );
            $builder->add(
                'distance',
                ChoiceType::class,
                [
                    'required' => true,
                    'choices'  => [

                        '0 km'   => 0,
                        '10 km'  => 10,
                        '50 km'  => 50,
                        '100 km' => 100,
                    ],
                ]
            );
        }
        $builder->add('submit', SubmitType::class, ['label' => 'Rechercher']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'city' => null,
            ]
        );
        $resolver->setAllowedTypes('city', ['null', 'City']);
    }
}
