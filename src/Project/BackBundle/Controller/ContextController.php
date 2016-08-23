<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 19.07.16
 * Time: 05:40
 */

namespace Project\BackBundle\Controller;

use Project\FrontBundle\Entity\Context;
use Project\FrontBundle\Form\Admin\ContextAdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class CategoryController
 * @package Project\BackBundle\Controller
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Route("/context")
 */
class ContextController extends AbstractCRUDController implements BackControllerInterface
{

    public function getClassName()
    {
        return 'Context';
    }

    public function getClass()
    {
        return Context::class;
    }

    public function getForm()
    {
        return ContextAdminType::class;
    }
}

