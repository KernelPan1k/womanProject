<?php

namespace Project\BackBundle\Datatables;

use Project\FrontBundle\Entity\Forum;
use Sg\DatatablesBundle\Datatable\Column\ColumnBuilder;

/**
 * Class ForumDatatable
 *
 * @package Project\FrontBundle\Datatables
 */
class ForumDatatable extends AbstractDatatable implements DatatableInterface
{

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return Forum::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'forum_datatable';
    }

    /**
     * @return string
     */
    public function getClassname()
    {
        return 'forum';
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
                'name',
                'column',
                [
                    'title' => 'Nom',
                ]
            )
            ->add(
                'category.name',
                'column',
                [
                    'title' => 'Categorie',
                ]
            );
    }
}
