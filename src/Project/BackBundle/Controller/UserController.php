<?php

namespace Project\BackBundle\Controller;

use Application\UserBundle\Entity\User;
use Application\UserBundle\Form\AvatarType;
use Application\UserBundle\Form\BackgroundType;
use Application\UserBundle\Form\ProfileUserAdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package Project\BackBundle\Controller
 * @Route("/users")
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class UserController extends AbstractCRUDController implements BackControllerInterface
{

    /**
     * @param $id
     * @Route("/{id}/display", name="project_back_user_display", requirements={"id","\d+"}, options={"expose"=true})
     * @Method("GET")
     *
     * @return Response
     */
    public function displayAction(int $id)
    {
        $em   = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if (null === $user) {
            throw $this->createNotFoundException("Unknow user $id");
        }
        $mandatory = $this->get("validator")->validate($user, null, ['mandatory']);

        return $this->render(
            'ProjectBackBundle:User:display.html.twig',
            ['object' => $user, 'mandatory' => $mandatory]
        );
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return RedirectResponse|Response
     * @Route("/uplaod/{id}/avatar", name="project_back_user_avatar", requirements={"id":"\d+"})
     * @Method({"GET", "POST"})
     *
     */
    public function avatarAction(Request $request, $id)
    {
        return $this->pictureHandler($request, $id, AvatarType::class);
    }

    /**
     * @param Request $request
     * @param  int    $id
     * @param string  $formType
     *
     * @return RedirectResponse|Response
     *
     */
    private function pictureHandler(Request $request, $id, $formType)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->find($id);
        if (null === $user) {
            throw $this->createNotFoundException("Unknow user $id");
        }
        $form = $this->createForm(
            $formType,
            $user,
            ['action' => $this->generateUrl('project_back_user_avatar', ['id' => $id])]
        );
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('project_back_user_display', ['id' => $id]);
        } elseif ($request->isMethod('POST')) {
            $errors = $form->getErrors(true);
            foreach ($errors as $e) {
                $message = $e->getMessage();
                if (!empty($message)) {
                    $this->addFlash('danger', $message);
                }
            }

            return $this->redirectToRoute('project_back_user_display', ['id' => $id]);
        }

        return $this->render('ApplicationUserBundle:User:form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return RedirectResponse|Response
     * @Route("/uplaod/{id}/background", name="project_back_user_background", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     *
     */
    public function backgroundAction(Request $request, $id)
    {
        return $this->pictureHandler($request, $id, BackgroundType::class);
    }

    /**
     * @param int $id
     * @param     $token
     *
     * @return RedirectResponse
     * @Route("/{id}/lock/{token}", name="project_back_user_lock", requirements={"id":"\d+"})
     * @Method({"GET"})
     */
    public function lockAction($id, $token)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->check($id, 'lock', $token);
        $lock = !$user->isLocked();
        $user->setLocked($lock);
        $em->flush();
        $this->addFlash('warning', "L'utilisateur $id a bien été ".($lock) ? "bloqué" : "débloqué");

        return $this->redirectToRoute('project_back_user_display', ['id' => $id]);
    }

    public function getClassName()
    {
        return 'User';
    }

    public function getClass()
    {
        return User::class;
    }

    public function getForm()
    {
        return ProfileUserAdminType::class;
    }
}
