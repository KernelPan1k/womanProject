<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 21.07.16
 * Time: 20:22
 */

namespace Project\BackBundle\Datatables;

    /**
     * Interface DatatableInterface
     * @package Project\BackBundle\Datatables
     */
/**
 * Interface DatatableInterface
 * @package Project\BackBundle\Datatables
 */
interface DatatableInterface
{
    /**
     * @return string
     */
    public function getEntity();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getClassname();
}
