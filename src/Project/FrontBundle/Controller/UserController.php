<?php

namespace Project\FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class UserController
 * @package Project\FrontBundle\Controller
 * @Route("/utilisateur")
 */
class UserController extends Controller
{

    /**
     * @Route("/amies", name="project_front_user_friends")
     * @Method({"GET"})
     */
    public function friendsAction()
    {
        return $this->render('ProjectFrontBundle:User:friends.html.twig');
    }

    /**
     * @Route("/amies/publications", name="project_front_user_post")
     * @Method({"GET"})
     */
    public function postAction()
    {
        return $this->render('ProjectFrontBundle:User:post.html.twig');
    }
}
