<?php

namespace Project\BackBundle\Datatables;

use Project\FrontBundle\Entity\Context;
use Sg\DatatablesBundle\Datatable\Column\ColumnBuilder;

/**
 * Class ContextDatatable
 * @package Project\BackBundle\Datatables
 */
class ContextDatatable extends AbstractDatatable implements DatatableInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return Context::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'context_datatable';
    }

    /**
     * @return string
     */
    public function getClassname()
    {
        return 'context';
    }

    /**
     * @param ColumnBuilder $builder
     *
     * @return ColumnBuilder
     */
    protected function config(ColumnBuilder $builder)
    {
        return $builder
            ->add(
                'id',
                'column',
                [
                    'title' => 'Id',
                ]
            )
            ->add(
                'context',
                'column',
                [
                    'title' => 'Nom',
                ]
            );
    }
}
