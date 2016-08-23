<?php
namespace Project\FrontBundle\Form\Select2;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Select2Type
 * @package Project\FrontBundle\Form\Select2
 */
class Select2Type extends AbstractType
{
    public function getParent()
    {
        return ChoiceType::class;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars["attr"] = array_merge(["data-select2-options" => json_encode([])], $view->vars["attr"]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("select2", []);
        $resolver->setAllowedTypes("select2", "array");
    }
}
