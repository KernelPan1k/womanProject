<?php

namespace Project\BackBundle\Controller;

use Application\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="project_back_default_index")
     */
    public function indexAction()
    {
        return $this->render('ProjectBackBundle:Default:index.html.twig');
    }

    public function statistiqueAction()
    {
        $em           = $this->getDoctrine()->getManager();
        $user         = count($em->getRepository(User::class)->findAll());
        $userComplete = count($em->getRepository(User::class)->findComplete());

        return $this->render(
            'ProjectBackBundle:Default:counter.html.twig',
            [
                'user'         => $user,
                'userComplete' => $userComplete,
            ]
        );
    }
}
