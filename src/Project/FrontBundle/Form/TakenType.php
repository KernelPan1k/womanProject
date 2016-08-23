<?php

namespace Project\FrontBundle\Form;

use Project\FrontBundle\Entity\Taken;
use Project\FrontBundle\Form\ProtoForm\AbstractTakenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TakenType extends AbstractTakenType
{
    /**
     * @var TokenStorageInterface $storageInterface
     */
    private $storageInterface;

    /**
     * TakenType constructor.
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
        parent::buildForm($builder, $options);
        $builder->add('submit', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-success']]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var Taken $taken */
                $taken = $event->getData();
                $user  = $this->storageInterface->getToken()->getUser();
                $city  = $user->getCity();
                if (null !== $city) {
                    $taken->setCity($city);
                }
            }
        );

    }
}
