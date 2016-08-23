<?php

namespace Project\FrontBundle\Form;

use Application\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Project\FrontBundle\Entity\Category;
use Project\FrontBundle\Entity\City;
use Project\FrontBundle\Entity\Context;
use Project\FrontBundle\Entity\Region;
use Project\FrontBundle\Form\Select2\AjaxChoiceType;
use Project\FrontBundle\Form\Select2\Select2EntityType;
use Project\FrontBundle\Form\Select2\Select2Type;
use Project\FrontBundle\Services\NetworkSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class NetworkType
 * @package Project\FrontBundle\Form
 */
class NetworkType extends AbstractType
{
    /**
     * @var TokenStorageInterface $storageInterface
     */
    private $storageInterface;

    /**
     * NetworkType constructor.
     *
     * @param TokenStorageInterface $storageInterface
     */
    public function __construct(TokenStorageInterface $storageInterface)
    {
        $this->storageInterface = $storageInterface;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'categorys',
            Select2EntityType::class,
            [
                'class'         => Category::class,
                'choice_label'  => 'name',
                'multiple'      => true,
                'label'         => 'Intérêt',
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
            'type',
            Select2Type::class,
            [
                'label'    => 'Ville/Région',
                'choices'  => [
                    'Région' => NetworkSearch::REGION,
                    'Ville'  => NetworkSearch::CITY,
                ],
                'expanded' => false,
                'multiple' => false,
            ]
        );

        $builder->add(
            'city',
            AjaxChoiceType::class,
            [
                'label'        => 'Ville',
                'view_class'   => City::class,
                'route_name'   => 'form_city_query',
                'choice_label' => 'name',
                'required'     => false,
            ]
        );

        $builder->add(
            'distance',
            Select2Type::class,
            [
                'label'   => 'Distance',
                'choices' => [

                    '0 km'   => 0,
                    '10 km'  => 10,
                    '20 km'  => 20,
                    '50 km'  => 50,
                    '100 km' => 100,
                    '150 km' => 150,
                    '200 km' => 200,

                ],
            ]
        );

        $builder->add(
            'ageMin',
            IntegerType::class,
            [
                'attr' => [
                    'style' => 'padding-right:0',
                ],
            ]
        );
        $builder->add(
            'ageMax',
            IntegerType::class,
            [
                'attr' => [
                    'style' => 'padding-right:0',
                ],
            ]
        );
        $builder->add(
            'search',
            SearchType::class,
            [
                'label' => 'Mot clés',
            ]
        );
        $builder->add(
            'send',
            SubmitType::class,
            [
                'label' => 'Rechercher',
            ]
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var User|null $user */
                $user    = $this->storageInterface->getToken()->getUser();
                $form    = $event->getForm();
                $options = [
                    'label'        => 'Région',
                    'class'        => Region::class,
                    'choice_label' => function ($r) {
                        /** @var Region $r */
                        return $r->getName().' - '.$r->getCountry()->getName();
                    },
                ];

                if (null !== $user && $user instanceof User && null !== $user->getCity()) {
                    $options = array_merge($options, ['data' => $user->getCity()->getRegion()]);
                }

                $form->add('region', Select2EntityType::class, $options);
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'required' => false,
                'attr'     => [
                    'class' => 'form-control',
                ],
            ]
        );
    }
}
