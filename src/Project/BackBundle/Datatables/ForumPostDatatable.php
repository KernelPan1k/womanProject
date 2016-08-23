<?php

namespace Project\BackBundle\Datatables;

use Project\FrontBundle\Entity\ForumPost;
use Sg\DatatablesBundle\Datatable\Column\ColumnBuilder;

/**
 * Class ForumPostDatatable
 *
 * @package Project\FrontBundle\Datatables
 */
class ForumPostDatatable extends AbstractDatatable implements DatatableInterface
{

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return ForumPost::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'forum_post_datatable';
    }

    /**
     * @return string
     */
    public function getClassname()
    {
        return 'forumpost';
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
                'forum.name',
                'column',
                [
                    'title' => 'Forum',
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
                'createdAt',
                'datetime',
                [
                    'title' => 'Créer le',
                ]
            );
    }
}
