<?php

namespace Project\FrontBundle\Form\Select2;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Select2EntityType
 * @package Project\FrontBundle\Form\Select2
 */
class Select2EntityType extends Select2Type
{
    public function getParent()
    {
        return EntityType::class;
    }

    public function getBlockPrefix()
    {
        return 'select2';
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);

        if ($options['add_extra_field'] == true) {
            $name  = $view->vars['name'];
            $label = "Ajouter un nouveau $name ?";
            if (!empty($options['extra_field_label'])) {
                $label = $options['extra_field_label'];
            }
            $new_choice              = new ChoiceView([], 'add', $label);
            $view->vars['choices'][] = $new_choice;
        }

    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('add_extra_field', false);
        $resolver->setAllowedTypes("add_extra_field", "boolean");
        $resolver->setDefault('extra_field_label', null);
    }

}
