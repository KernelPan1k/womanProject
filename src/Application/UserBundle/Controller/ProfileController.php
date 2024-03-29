<?php

namespace Application\UserBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as BaseController;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ProfileController
 * @package Application\UserBundle\Controller
 */
class ProfileController extends BaseController
{
    /**
     * @return Response
     */
    public function showAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $mandatory = $this->get("validator")->validate($user, null, ['mandatory']);

        return $this->render(
            'FOSUserBundle:Profile:show.html.twig',
            [
                'user'      => $user,
                'mandatory' => $mandatory,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return null|RedirectResponse|Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url      = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(
                FOSUserEvents::PROFILE_EDIT_COMPLETED,
                new FilterUserResponseEvent($user, $request, $response)
            );

            return $response;

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

        return $this->render(
            'FOSUserBundle:Profile:edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
