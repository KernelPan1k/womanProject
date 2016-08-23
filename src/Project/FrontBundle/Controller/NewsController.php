<?php

namespace Project\FrontBundle\Controller;

use Application\UserBundle\Entity\User;
use Project\FrontBundle\Entity\News;
use Project\FrontBundle\Entity\NewsComment;
use Project\FrontBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NewsController
 * @package Project\FrontBundle\Controller
 * @Route("/actualités")
 */
class NewsController extends Controller
{

    /**
     * @Route("/{page}", name="project_front_news_index", requirements={"page":"\d+"}, defaults={"page":1})
     * @Method({"GET"})
     * @param int $page
     *
     * @return Response
     */
    public function indexAction(int $page)
    {
        $em        = $this->getDoctrine()->getManager();
        $news      = $em->getRepository(News::class)->findActive();
        $paginator = $this->get('knp_paginator');
        $object    = $paginator->paginate($news, $page, 10);

        return $this->render('ProjectFrontBundle:News:index.html.twig', ['object' => $object]);
    }

    /**
     * @Route("/{slug}", name="project_front_news_display", requirements={"slug" : "(.+)"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param string  $slug
     *
     * @return Response
     */
    public function displayAction(Request $request, string $slug)
    {
        $form = null;
        $user = $this->getUser();
        $em   = $this->getDoctrine()->getManager();
        $news = $em->getRepository(News::class)->findOneNewsDisplay($slug);
        if (null === $news) {
            throw $this->createNotFoundException("Unknow news");
        }
        if (null !== $user && $user instanceof User) {
            $comment = new NewsComment();
            $form    = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $comment->setNews($news);
                $comment->setUser($user);
                $em->persist($comment);
                $em->flush();
                $this->addFlash('success', "Votre commentaire a été enregistré");
            }
        }
        $form = null === $form ? $form : $form->createView();

        return $this->render(
            'ProjectFrontBundle:News:display.html.twig',
            ['object' => $news, 'form' => $form]
        );
    }
}
