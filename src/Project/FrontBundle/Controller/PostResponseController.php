<?php

namespace Project\FrontBundle\Controller;

use Application\UserBundle\Entity\User;
use Project\FrontBundle\Entity\ForumPost;
use Project\FrontBundle\Entity\PostResponse;
use Project\FrontBundle\Form\CommentPostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ForumController
 * @package Project\FrontBundle\Controller
 * @Route("/discution")
 */
class PostResponseController extends Controller
{
    /**
     * @Route(
     *     "/{page}/{forum_slug}/{post_slug}",
     *     name="project_front_postresponse_display",
     *     requirements={"forum_slug":"(.+)","post_slug":"(.+)", "page":"\d+"},
     *     defaults={"page":1})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param string  $forum_slug
     * @param string  $post_slug
     *
     * @param int     $page
     *
     * @return Response
     */
    public function displayAction(Request $request, string $forum_slug, string $post_slug, int $page)
    {
        $form      = null;
        $user      = $this->getUser();
        $perpage   = $this->getParameter('pagination_per_page');
        $paginator = $this->get('knp_paginator');
        $em        = $this->getDoctrine()->getManager();
        $post      = $em->getRepository(ForumPost::class)->findOneBy(['slug' => $post_slug]);
        if (null === $post) {
            throw $this->createNotFoundException("Unknow post");
        }
        if (null !== $user && $user instanceof User) {
            $comment = new PostResponse();
            $comment->setPost($post);
            $form = $this->createForm(CommentPostType::class, $comment);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $parentId = intval($form->get('parent')->getData());
                if (null !== $parentId && 0 < $parentId) {
                    $parentResponse = $post->getParentResponseById($parentId);
                    if (null !== $parentResponse && $parentResponse instanceof PostResponse) {
                        $comment->setParent($parentResponse);
                    }
                }
                $comment->setUser($user);
                $comment->setPost($post);
                $em->persist($comment);
                $em->flush();
                $this->addFlash('success', "Votre message a été enregistré");
                $comment = new PostResponse();
                $comment->setPost($post);
                $form = $this->createForm(CommentPostType::class, $comment);
            }
        }

        $query   = $em->getRepository(PostResponse::class)->findResponseByPost($forum_slug, $post);
        $objects = $paginator->paginate($query, $page, $perpage);
        $form    = null === $form ? $form : $form->createView();

        return $this->render(
            'ProjectFrontBundle:Forum:display.html.twig',
            ['objects' => $objects, 'post' => $post, 'form' => $form]
        );
    }
}
