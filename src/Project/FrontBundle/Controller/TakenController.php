<?php

namespace Project\FrontBundle\Controller;

use Application\UserBundle\Entity\User;
use Project\FrontBundle\Entity\Taken;
use Project\FrontBundle\Entity\TakenComment;
use Project\FrontBundle\Entity\TakenParticipate;
use Project\FrontBundle\Form\CommentType;
use Project\FrontBundle\Form\TakenParticipateType;
use Project\FrontBundle\Form\TakenSearchType;
use Project\FrontBundle\Form\TakenType;
use Project\FrontBundle\Form\UnscribeType;
use Project\FrontBundle\Voter\TakenVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TakenController
 * @package Project\FrontBundle\Controller
 * @Route("/sorties")
 */
class TakenController extends Controller
{
    /**
     * @param Request $request
     * @param int     $page
     *
     * @return Response
     * @Route("/{page}", name="project_front_taken_index", requirements={"page":"\d+"}, defaults={"page":1})
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request, $page)
    {
        $perPage   = $this->getParameter('pagination_per_page');
        $search    = $this->get('project.front_bundle.services.taken_search');
        $paginator = $this->get('knp_paginator');
        $user      = $this->getUser();
        $city      = null;
        $params    = [];
        if ($user instanceof User && null !== $user->getCity()) {
            $city = $user->getCity();
        }
        $form = $this->createForm(TakenSearchType::class, ['city' => $city]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $params = $form->getData();
        }
        $search->init($params);
        $query   = $search->search();
        $objects = $paginator->paginate($query, $page, $perPage);

        return $this->render(
            'ProjectFrontBundle:Taken:index.html.twig',
            [
                'objects' => $objects,
                'form'    => $form->createView(),
            ]
        );
    }


    /**
     * Creates a new Taken entity.
     *
     * @Route("/nouvelle", name="project_front_taken_create")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @Security("has_role('ROLE_USER')")
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $object = new Taken();
        $form   = $this->createForm(TakenType::class, $object);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $object->setUser($this->getUser());
            $em->persist($object);
            $em->flush();
            $this->addFlash('success', 'Votre sortie a été enregistrée avec succès');

            return $this->redirectToRoute('project_front_taken_display', ['slug' => $object->getSlug()]);
        }

        return $this->render('ProjectFrontBundle:Taken:create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param  string $slug
     *
     * @return Response
     * @Route("/{slug}", name="project_front_taken_display")
     * @Method({"GET", "POST"})
     */
    public function displayAction(Request $request, $slug)
    {
        $em           = $this->getDoctrine()->getManager();
        $form         = null;
        $commentForm  = null;
        $unscribeForm = null;
        /** @var User $user */
        $user = $this->getUser();
        /** @var Taken $object */
        $object = $em->getRepository(Taken::class)->findOneTakenBySlug($slug);
        if (null === $object) {
            throw $this->createNotFoundException("Unknow taken");
        }
        if ($this->isGranted(TakenVoter::SUBSCRIBE, $object)) {
            $participate = new TakenParticipate();
            $form        = $this->createForm(TakenParticipateType::class, $participate);
            $form->handleRequest($request);
            if ($form->isValid()) {
                if (($object->getNbrPossibleSubscriber() - $participate->getNbrPerson()) < 0) {
                    $form->get('nbrPerson')->addError(
                        new FormError("Il ne reste que ".$object->getNbrPossibleSubscriber()." places disponibles")
                    );
                } else {
                    $participate->setUser($user);
                    $participate->setTaken($object);
                    $em->persist($participate);
                    $em->flush();
                    $this->addFlash('success', "Vous venez de vous inscrire");
                    $form = null;
                }
            }
        }
        if ($this->isGranted(TakenVoter::UNSUBSCRIBE, $object)) {
            $unscribeForm = $this->createForm(UnscribeType::class);
            $unscribeForm->handleRequest($request);
            if ($unscribeForm->isValid()) {
                $object->removeParticipate($object->getParticipateByUser($user));
                $em->flush();
                $this->addFlash('success', "Vous venez de vous désinscrire");
                $unscribeForm = null;
                $participate  = new TakenParticipate();
                $form         = $this->createForm(TakenParticipateType::class, $participate);
            }
        }
        if ($this->isGranted(TakenVoter::COMMENT, $object)) {
            $comment     = new TakenComment();
            $commentForm = $this->createForm(CommentType::class, $comment);
            $commentForm->handleRequest($request);
            if ($commentForm->isValid()) {
                $comment->setUser($user);
                $comment->setTaken($object);
                $em->persist($comment);
                $em->flush();
                $this->addFlash('success', "Votre commentaire a été enregistré");
            }
        }

        $form         = null === $form ? $form : $form->createView();
        $commentForm  = null === $commentForm ? $commentForm : $commentForm->createView();
        $unscribeForm = null === $unscribeForm ? $unscribeForm : $unscribeForm->createView();

        return $this->render(
            'ProjectFrontBundle:Taken:display.html.twig',
            [
                'object'       => $object,
                'form'         => $form,
                'commentForm'  => $commentForm,
                'unscribeForm' => $unscribeForm,
            ]
        );
    }
}
