<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 21.07.16
 * Time: 05:35
 */

namespace Project\BackBundle\Controller;

/**
 * Interface BackControllerInterface
 * @package Project\BackBundle\Controller
 */
interface BackControllerInterface
{
    public function getClassName();

    public function getClass();

    public function getForm();
}
