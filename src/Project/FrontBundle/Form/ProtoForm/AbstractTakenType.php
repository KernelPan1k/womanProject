<?php


namespace Project\FrontBundle\Form\ProtoForm;

use Doctrine\ORM\EntityRepository;
use Project\FrontBundle\Entity\Category;
use Project\FrontBundle\Entity\City;
use Project\FrontBundle\Entity\Context;
use Project\FrontBundle\Entity\Taken;
use Project\FrontBundle\Form\Select2\AjaxChoiceType;
use Project\FrontBundle\Form\Select2\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class AbstractTakenType
 * @package Project\FrontBundle\Form\ProtoForm
 */
abstract class AbstractTakenType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('description', TextareaType::class, ['label' => "Description"])
            ->add(
                'startDate',
                DateTimeType::class,
                [
                    'widget'      => 'single_text',
                    'label'       => 'Date de début',
                    'format'      => 'dd/MM/yyyy',
                    'html5'       => false,
                    'attr'        => [
                        'placeholder' => 'jj/mm/aaaa ex: 21/03/1980',
                    ],
                    'constraints' => [new DateTime()],
                ]
            )
            ->add(
                'endDate',
                DateTimeType::class,
                [
                    'widget'      => 'single_text',
                    'label'       => 'Date de fin',
                    'required'    => false,
                    'format'      => 'dd/MM/yyyy',
                    'html5'       => false,
                    'attr'        => [
                        'placeholder' => 'jj/mm/aaaa ex: 21/03/1980',
                    ],
                    'constraints' => [new DateTime()],
                ]
            )
            ->add(
                'nbrPerson',
                IntegerType::class,
                [
                    'label'    => 'Nombre de personne',
                    'required' => false,
                ]
            )
            ->add(
                'price',
                NumberType::class,
                [
                    'label'    => "Prix",
                    'required' => false,
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'alt',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
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
                            ->setParameter('context', Context::CONTEXT_TAKEN);
                    },
                ]
            )
            ->add(
                'city',
                AjaxChoiceType::class,
                [
                    'view_class'   => City::class,
                    'label'        => 'Ville',
                    'route_name'   => 'form_city_query',
                    'choice_label' => 'name',
                    'attr'         => [
                        'class' => 'col-md-12 col-sm-12 col-xs-12',
                    ],
                ]
            )
            ->add('car', CheckboxType::class, ['label' => "Je dispose d'une voiture", 'required' => false])
            ->add('carPlace', IntegerType::class, ['label' => "Nombre de place disponible", 'required' => false]);

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $form  = $event->getForm();
                $start = $form->getData()->getStartDate();
                $end   = $form->getData()->getEndDate();
                $now   = new \DateTime();
                if ($now > $start || $now > $end) {
                    $form->get('startDate')->addError(
                        new FormError("La date de la sortie ne peut pas être antérieur à aujourd'hui")
                    );
                }
                if ($start > $end) {
                    $form->get('startDate')->addError(
                        new FormError("La date de départ ne peut être plus grande que celle de fin")
                    );
                }
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
                'data_class' => Taken::class,
                'required'   => true,
            ]
        );
    }
}
