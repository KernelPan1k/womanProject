<?php

namespace Project\BackBundle\Datatables;

use Project\FrontBundle\Entity\News;
use Sg\DatatablesBundle\Datatable\Column\ColumnBuilder;

/**
 * Class NewsDatatable
 *
 * @package Project\FrontBundle\Datatables
 */
class NewsDatatable extends AbstractDatatable implements DatatableInterface
{

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return News::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'news_datatable';
    }

    /**
     * @return string
     */
    public function getClassname()
    {
        return 'news';
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
                'category.name',
                'column',
                [
                    'title' => 'Categorie',
                ]
            )
            ->add(
                'createdAt',
                'datetime',
                [
                    'title' => 'Créer le',
                ]
            )
            ->add(
                'enabled',
                'column',
                [
                    'title' => 'Actif',
                ]
            );
    }
}
