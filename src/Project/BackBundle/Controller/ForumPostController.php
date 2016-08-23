<?php

namespace Project\BackBundle\Controller;

use Project\FrontBundle\Entity\ForumPost;
use Project\FrontBundle\Form\Admin\ForumPostAdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class ForumPostController
 * @package Project\BackBundle\Controller
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Route("/forum-post")
 */
class ForumPostController extends AbstractCRUDController implements BackControllerInterface
{

    public function getClassName()
    {
        return 'ForumPost';
    }

    public function getClass()
    {
        return ForumPost::class;
    }

    public function getForm()
    {
        return ForumPostAdminType::class;
    }
}
