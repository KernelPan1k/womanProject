<?php

namespace Project\FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class IndexController
 * @package Project\FrontBundle\Controller
 * @Route("/")
 */
class IndexController extends Controller
{
    /**
     * @Route("/", name="project_front_index_index")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        return $this->render('ProjectFrontBundle:Index:index.html.twig');
    }
}
