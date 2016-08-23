<?php


namespace Project\FrontBundle\Controller;

use Application\UserBundle\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Project\FrontBundle\Entity\City;
use Project\FrontBundle\Entity\ForumPost;
use Project\FrontBundle\Entity\PostResponse;
use Project\FrontBundle\Form\Select2\AjaxChoiceResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FormController
 * @package Project\FrontBundle\Controller
 * @Route("/_ajax")
 */
class FormController extends Controller
{
    /**
     * @param Request $request
     * @Route("/city", name="form_city_query")
     *
     * @return AjaxChoiceResponse
     */
    public function cityAction(Request $request)
    {
        $query   = $this->baseSearch($request, City::class);
        $objects = $query['objects'];
        $more    = $query['more'];

        return new AjaxChoiceResponse(
            function ($city) {
                /** @var City $city */
                return $city->getName()." - ".$city->getCp()." - ".$city->getRegion()->getCountry()->getName();
            },
            $objects,
            $more
        );
    }

    /**
     * @param Request $request
     * @param string  $repo
     *
     *
     * @return array
     */
    private function baseSearch(Request $request, $repo)
    {
        if (false == $request->isXmlHttpRequest() && $this->getParameter("kernel.debug") === false) {
            throw $this->createAccessDeniedException("Ajax only");
        }

        $term     = $request->query->get("q");
        $page     = (int)$request->query->get("page", 1);
        $perPage  = (int)$request->query->get("perPage", 20);
        $objectQb = $this->getDoctrine()->getManager()->getRepository($repo)->search($term);
        /** @var PaginationInterface $objects */
        $objects = $this->get("knp_paginator")->paginate($objectQb, $page, $perPage);
        /** @var SlidingPagination $objects */
        $pagination = $objects instanceof SlidingPagination ? $objects->getPaginationData() : null;
        $more       = $pagination !== null && $page < $pagination["last"];

        return ['objects' => $objects, 'more' => $more];
    }

    /**
     * @param Request $request
     * @Route("/user", name="form_user_query")
     *
     * @return AjaxChoiceResponse
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function userAction(Request $request)
    {
        $query   = $this->baseSearch($request, User::class);
        $objects = $query['objects'];
        $more    = $query['more'];

        return new AjaxChoiceResponse(
            function ($object) {
                /** @var User $object */
                return $object->getId().' - '.$object->getUsername();
            },
            $objects,
            $more
        );

    }

    /**
     * @param Request $request
     * @Route("/forumpost", name="form_forumpost_query")
     *
     * @return AjaxChoiceResponse
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function forumPostAction(Request $request)
    {
        $query   = $this->baseSearch($request, ForumPost::class);
        $objects = $query['objects'];
        $more    = $query['more'];

        return new AjaxChoiceResponse(
            function ($object) {
                /** @var ForumPost $object */
                return $object->getId().' - '.$object->getUser()->getUsername().' - '.$object->getTitle();
            },
            $objects,
            $more
        );
    }

    /**
     * @param Request $request
     * @Route("/forumresponse", name="form_forumresponse_query")
     *
     * @return AjaxChoiceResponse
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function postResponseAction(Request $request)
    {
        $query   = $this->baseSearch($request, PostResponse::class);
        $objects = $query['objects'];
        $more    = $query['more'];

        return new AjaxChoiceResponse(
            function ($object) {
                /** @var PostResponse $object */
                return $object->getId().' '.$object->getMessage();
            },
            $objects,
            $more
        );
    }
}
