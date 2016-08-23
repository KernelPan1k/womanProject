<?php

namespace Application\UserBundle\Form;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RegistrationType
 * @package Project\FrontBundle\Form
 */
class RegistrationType extends AbstractType
{
    /**
     * @var TranslatorInterface $translator
     */
    private $translator;

    /**
     * RegistrationType constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'firstName',
            TextType::class
        );
        $builder->add(
            'lastName',
            TextType::class
        );
        $builder->add(
            'dateOfBirth',
            DateType::class,
            [
                'widget'      => 'single_text',
                'format'      => 'dd/MM/yyyy',
                'html5'       => false,
                'attr'        => [
                    'placeholder' => 'jj/mm/aaaa ex: 21/03/1980',
                ],
                'constraints' => [new DateTime()],
            ]
        );
        $builder->add(
            'cgu',
            CheckboxType::class,
            [
                'mapped' => false,
                'label'  => false,
            ]
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {

                $birth = $event->getForm()->get('dateOfBirth');
                $min   = new \DateTime('-18 year');
                $max   = new \DateTime('-99 year');

                if ($birth->getData() > $min) {
                    $birth->addError(
                        new FormError($this->translator->trans('error.message.min.year.old', [], 'messages'))
                    );
                }

                if ($birth->getData() < $max) {
                    $birth->addError(
                        new FormError($this->translator->trans('error.message.max.year.old', [], 'messages'))
                    );
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'required'   => true,
                'constraint' => [new NotBlank()],
            ]
        );
    }

    public function getParent()
    {
        return RegistrationFormType::class;
    }

    public function getBlockPrefix()
    {
        return 'entreamies_user_registration';
    }
}
