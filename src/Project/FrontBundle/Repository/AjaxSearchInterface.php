<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 01.08.16
 * Time: 07:47
 */

namespace Project\FrontBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * Interface AjaxSearchInterface
 * @package Project\FrontBundle\Repository
 */
interface AjaxSearchInterface
{
    /**
     * @param $term
     *
     * @return QueryBuilder
     */
    public function search($term);
}
