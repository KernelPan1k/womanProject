<?php

namespace Project\BackBundle\Datatables;

use Project\FrontBundle\Entity\Category;
use Sg\DatatablesBundle\Datatable\Column\ColumnBuilder;

/**
 * Class CategoryDatatable
 * @package Project\BackBundle\Datatables
 */
class CategoryDatatable extends AbstractDatatable implements DatatableInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return Category::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'category_datatable';
    }

    /**
     * @return string
     */
    public function getClassname()
    {
        return 'category';
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
                'name',
                'column',
                [
                    'title' => 'Nom',
                ]
            );
    }
}
