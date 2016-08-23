<?php

namespace Project\FrontBundle\Controller;

use Application\UserBundle\Entity\User;
use Project\FrontBundle\Form\NetworkType;
use Project\FrontBundle\Repository\UserRepository;
use Project\FrontBundle\Services\NetworkSearch;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NetworkController
 * @package Project\FrontBundle\Controller
 * @Route("/reseaux")
 */
class NetworkController extends Controller
{
    /**
     * @Route("/{page}", name="project_front_network_index", defaults={"page" : 1 })
     * @Method({"GET", "POST"})
     * @param Request $request
     *
     * @param int     $page
     *
     * @return Response
     */
    public function indexAction(Request $request, int $page)
    {
        $form            = $this->createForm(NetworkType::class);
        $paginator       = $this->get('knp_paginator');
        $search          = $this->get('project.front_bundle.services.network_search');
        $perPage         = $this->getParameter('pagination_per_page');
        $objects         = null;
        $userByCity      = null;
        $userByCategorys = null;
        $userByOld       = null;

        $form->handleRequest($request);
        if ($form->isValid() && $request->isMethod('POST')) {
            $query   = $search->search($form->getData());
            $objects = $paginator->paginate($query, $page, $perPage);
            $objects->setTemplate('ProjectFrontBundle:Default:pagination_single.html.twig');
            if ($request->isXmlHttpRequest()) {
                return $this->render(
                    'ProjectFrontBundle:Network:include-network-index-list.html.twig',
                    [
                        'objects' => $objects,
                    ]
                );
            }
        } elseif ($request->isMethod('GET')) {
            $user = $this->getUser();
            if (null !== $user && $user instanceof User && null !== $user->getCity()) {
                $params = ['type' => NetworkSearch::REGION, 'region' => $user->getCity()->getRegion()];
                $query  = $search->search($params);
                if ($user->isProfilComplete()) {
                    /** @var UserRepository $repo */
                    $repo            = $this->getDoctrine()->getManager()->getRepository(User::class);
                    $userByCity      = $repo->findRandByCity($user);
                    $userByCategorys = $repo->findRandByCategorys($user);
                    $userByOld       = $repo->findRandByOld($user);
                }
            } else {
                $query = $search->search([]);
            }
            $objects = $paginator->paginate($query, $page, $perPage);
            $objects->setTemplate('ProjectFrontBundle:Default:pagination_single.html.twig');
        } else {
            throw $this->createNotFoundException("Invalid request");
        }

        return $this->render(
            'ProjectFrontBundle:Network:index.html.twig',
            [
                'form'            => $form->createView(),
                'objects'         => $objects,
                'userByCity'      => $userByCity,
                'userByCategorys' => $userByCategorys,
                'userByOld'       => $userByOld,
            ]
        );
    }
}
