<?php

namespace Project\BackBundle\Controller;

use Project\FrontBundle\Entity\PostResponse;
use Project\FrontBundle\Form\Admin\PostResponseAdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class ForumPostController
 * @package Project\BackBundle\Controller
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Route("/post-response")
 */
class PostResponseController extends AbstractCRUDController implements BackControllerInterface
{

    public function getClassName()
    {
        return 'PostResponse';
    }

    public function getClass()
    {
        return PostResponse::class;
    }

    public function getForm()
    {
        return PostResponseAdminType::class;
    }
}
