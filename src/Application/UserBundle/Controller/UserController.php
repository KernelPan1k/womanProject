<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 27.06.16
 * Time: 06:56
 */

namespace Application\UserBundle\Controller;

use Application\UserBundle\Entity\User;
use Application\UserBundle\Form\AvatarType;
use Application\UserBundle\Form\BackgroundType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package Application\UserBundle\Controller
 * @Security("has_role('ROLE_USER')")
 * @Route("/membre")
 */
class UserController extends Controller
{
    /**
     * @param Request $request
     * @Route("/uplaod/avatar", name="application_user_user_avatar")
     * @Method({"GET", "POST"})
     *
     * @return RedirectResponse|Response
     */
    public function avatarAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(
            AvatarType::class,
            $user,
            ['action' => $this->generateUrl('application_user_user_avatar')]
        );
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('fos_user_profile_show');
        } elseif ($request->isMethod('POST')) {
            $errors = $form->getErrors(true);
            foreach ($errors as $e) {
                $message = $e->getMessage();
                if (!empty($message)) {
                    $this->addFlash('danger', $message);
                }
            }

            return $this->redirectToRoute('fos_user_profile_show');
        }

        return $this->render('ApplicationUserBundle:User:form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @Route("/uplaod/background", name="application_user_user_background")
     * @Method({"GET", "POST"})
     *
     * @return RedirectResponse|Response
     */
    public function backgroundAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(
            BackgroundType::class,
            $user,
            ['action' => $this->generateUrl('application_user_user_background')]
        );
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('fos_user_profile_show');
        } elseif ($request->isMethod('POST')) {
            $errors = $form->getErrors(true);
            foreach ($errors as $e) {
                $message = $e->getMessage();
                if (!empty($message)) {
                    $this->addFlash('danger', $message);
                }
            }

            return $this->redirectToRoute('fos_user_profile_show');
        }

        return $this->render('ApplicationUserBundle:User:form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param $slug
     *
     * @return RedirectResponse|Response
     * @Route("/profil/{slug}", name="application_userbundle_user_public_profil", requirements={"slug":"(.+)"})
     * @Security("has_role('ROLE_USER')")
     */
    public function publicProfilAction($slug)
    {
        $em   = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['slug' => $slug]);
        if (null === $user) {
            $this->addFlash('warning', "L'utilisateur n'existe pas !");

            return $this->redirectToRoute("project_front_network_index");
        }

        return $this->render('ApplicationUserBundle:User:display.html.twig', ['object' => $user]);
    }
}
