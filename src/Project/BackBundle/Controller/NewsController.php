<?php

namespace Project\BackBundle\Controller;

use Project\FrontBundle\Entity\News;
use Project\FrontBundle\Form\Admin\NewsAdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class NewsController
 * @package Project\BackBundle\Controller
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Route("/news")
 */
class NewsController extends AbstractCRUDController implements BackControllerInterface
{

    public function getClassName()
    {
        return 'News';
    }

    public function getClass()
    {
        return News::class;
    }

    public function getForm()
    {
        return NewsAdminType::class;
    }
}
