<?php

namespace Project\BackBundle\Datatables;

use Project\FrontBundle\Entity\PostResponse;
use Sg\DatatablesBundle\Datatable\Column\ColumnBuilder;

/**
 * Class PostResponseDatatable
 *
 * @package Project\FrontBundle\Datatables
 */
class PostResponseDatatable extends AbstractDatatable implements DatatableInterface
{

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return PostResponse::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'post_response_datatable';
    }

    /**
     * @return string
     */
    public function getClassname()
    {
        return 'postresponse';
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
                'post.id',
                'column',
                [
                    'title' => 'Id du post',
                ]
            )
            ->add(
                'user.username',
                'column',
                [
                    'title' => 'Utilisateur',
                ]
            )
            ->add(
                'parent.id',
                'column',
                [
                    'title' => 'Id du parent',
                ]
            )
            ->add(
                'createdAt',
                'datetime',
                [
                    'title' => 'Créer le',
                ]
            );
    }
}
