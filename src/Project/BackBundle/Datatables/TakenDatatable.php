<?php

namespace Project\BackBundle\Datatables;

use Project\FrontBundle\Entity\Taken;
use Sg\DatatablesBundle\Datatable\Column\ColumnBuilder;

/**
 * Class TakenDatatable
 *
 * @package Project\FrontBundle\Datatables
 */
class TakenDatatable extends AbstractDatatable implements DatatableInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return Taken::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'taken_datatable';
    }

    /**
     * @return string
     */
    public function getClassname()
    {
        return 'taken';
    }

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
                'title',
                'column',
                [
                    'title' => 'Titre',
                ]
            )
            ->add(
                'startDate',
                'datetime',
                [
                    'title' => 'Date de départ',
                ]
            )
            ->add(
                'category.name',
                'column',
                [
                    'title' => 'Catégorie',
                ]
            )
            ->add(
                'city.name',
                'column',
                [
                    'title' => 'Ville',
                ]
            );
    }
}
