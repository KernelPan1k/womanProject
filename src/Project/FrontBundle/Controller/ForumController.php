<?php

namespace Project\FrontBundle\Controller;

use Project\FrontBundle\Entity\Forum;
use Project\FrontBundle\Entity\ForumPost;
use Project\FrontBundle\Form\ForumPostType;
use Project\FrontBundle\Voter\ForumPostVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ForumController
 * @package Project\FrontBundle\Controller
 * @Route("/discutions")
 */
class ForumController extends Controller
{

    /**
     * @Route("/", name="project_front_forum_index")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        $em      = $this->getDoctrine()->getManager();
        $objects = $em->getRepository(Forum::class)->findAll();

        return $this->render('ProjectFrontBundle:Forum:index.html.twig', ['objects' => $objects]);
    }

    /**
     * Creates a new Taken entity.
     *
     * @Route("/nouvelle", name="project_front_forum_create")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @Security("has_role('ROLE_USER')")
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $object = new ForumPost();
        $form   = $this->createForm(ForumPostType::class, $object);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $object->setUser($this->getUser());
            $em->persist($object);
            $em->flush();
            $this->addFlash('success', 'Votre conversation a été enregistrée avec succès');
            $forum_slug = $object->getForum()->getSlug();
            $post_slug  = $object->getSlug();

            return $this->redirectToRoute(
                'project_front_postresponse_display',
                ['forum_slug' => $forum_slug, 'post_slug' => $post_slug]
            );
        }

        return $this->render('ProjectFrontBundle:Forum:create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Creates a new Taken entity.
     *
     * @Route(
     *     "/edit/{slug}",
     *     name="project_front_forum_edit",
     *     requirements={"slug":"(.+)"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param string  $slug
     *
     * @return Response
     * @Security("has_role('ROLE_USER')")
     *
     */
    public function editAction(Request $request, string $slug)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var ForumPost $object */
        $object = $em->getRepository(ForumPost::class)->findOneBy(['slug' => $slug]);
        if (null === $object) {
            throw $this->createNotFoundException("Unknow post");
        }
        $this->denyAccessUnlessGranted(ForumPostVoter::EDIT, $object);
        $form = $this->createForm(ForumPostType::class, $object);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->flush();
            $this->addFlash('success', "Votre message est modifié.");
            $forum_slug = $object->getForum()->getSlug();
            $post_slug  = $object->getSlug();

            return $this->redirectToRoute(
                'project_front_postresponse_display',
                ['forum_slug' => $forum_slug, 'post_slug' => $post_slug]
            );
        }

        return $this->render('ProjectFrontBundle:Forum:create.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("/{page}/{forum}",
     *     name="project_front_forum_list",
     *     requirements={"forum":"(.*)", "page" : "\d+"},
     *          defaults={"page":1})
     * @Method({"GET"})
     * @param string $forum
     * @param int    $page
     *
     * @return Response
     */
    public function listAction($forum, $page)
    {
        $perpage   = $this->getParameter('pagination_per_page');
        $em        = $this->getDoctrine()->getManager();
        $query     = $em->getRepository(ForumPost::class)->findByForumName($forum);
        $paginator = $this->get('knp_paginator');
        $objects   = $paginator->paginate($query, $page, $perpage);

        return $this->render('ProjectFrontBundle:Forum:list.html.twig', ['objects' => $objects]);
    }
}
