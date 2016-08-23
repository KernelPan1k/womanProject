<?php

namespace Project\BackBundle\Controller;

use Project\FrontBundle\Entity\Forum;
use Project\FrontBundle\Form\Admin\ForumAdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class ForumController
 * @package Project\BackBundle\Controller
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Route("/forum")
 */
class ForumController extends AbstractCRUDController implements BackControllerInterface
{
    public function getClassName()
    {
        return 'Forum';
    }

    public function getClass()
    {
        return Forum::class;
    }

    public function getForm()
    {
        return ForumAdminType::class;
    }
}
