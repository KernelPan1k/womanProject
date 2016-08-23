<?php

namespace Project\BackBundle\Datatables;

use Application\UserBundle\Entity\User;
use Sg\DatatablesBundle\Datatable\Column\ColumnBuilder;

/**
 * Class UserDatatable
 *
 * @package Application\UserBundle\Datatables
 */
class UserDatatable extends AbstractDatatable implements DatatableInterface
{

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return User::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user_datatable';
    }

    /**
     * @return string
     */
    public function getClassname()
    {
        return 'user';
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
                'username',
                'column',
                [
                    'title' => "Nom d'utilisateur",
                ]
            )
            ->add(
                'city.name',
                'column',
                [
                    'title' => 'Ville',
                ]
            )
            ->add(
                'city.cp',
                'column',
                [
                    'title' => 'Code postal',
                ]
            )
            ->add(
                'city.region.name',
                'column',
                [
                    'title' => 'Région',

                ]
            )->add(
                'city.region.country.name',
                'column',
                [
                    'title' => 'Pays',

                ]
            );
    }
}
