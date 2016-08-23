<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 21.07.16
 * Time: 05:34
 */

namespace Project\BackBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sg\DatatablesBundle\Datatable\View\DatatableViewInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractCRUDController
 * @package Project\BackBundle\Controller
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
abstract class AbstractCRUDController extends Controller
{

    const VIEW_CREATE = 1;
    const VIEW_DISPLAY = 2;
    const DATATABLE = 3;
    const ROUTE_DISPLAY = 4;
    const ROUTE_INDEX = 5;
    const ERROR_NOTFOUND = 6;
    const SUCCESS_SAVE = 7;
    const SUCCESS_EDIT = 8;
    const NOT_FOUND = "Impossible de trouver %s %d";

    /**
     * @Route("/")
     * @Method("GET")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var DatatableViewInterface $datatable */
        $datatable = $this->get($this->makePath(self::DATATABLE));
        $datatable->buildDatatable();
        if ($request->isXmlHttpRequest() || $request->get('debug') == 1) {
            $query = $this->get("sg_datatables.query")->getQueryFrom($datatable);

            return $query->getResponse();
        }

        return $this->render(
            'ProjectBackBundle:Default:datatable.html.twig',
            [
                'datatable' => $datatable,
            ]
        );
    }

    /**
     * @param int      $type
     * @param int|null $params
     *
     * @return string
     */
    protected function makePath(int $type, int $params = null)
    {
        switch ($type) {
            case self::ERROR_NOTFOUND:
                return sprintf(self::NOT_FOUND, $this->getClassName(), $params['id']);
                break;
            case self::VIEW_DISPLAY:
                return sprintf('ProjectBackBundle:%s:display.html.twig', $this->getClassName());
                break;
            case self::VIEW_CREATE:
                return sprintf('ProjectBackBundle:%s:create.html.twig', $this->getClassName());
                break;
            case self::DATATABLE:
                return sprintf("project.back_bundle.datatable.%s", mb_strtolower($this->getClassName()));
                break;
            case self::ROUTE_DISPLAY:
                return sprintf('project_back_%s_display', mb_strtolower($this->getClassName()));
                break;
            case self::ROUTE_INDEX:
                return sprintf('project_back_%s_index', mb_strtolower($this->getClassName()));
                break;
            case self::SUCCESS_SAVE:
                return sprintf("L'entité %s à bien été enregistrée", $this->getClassName());
                break;
            case self::SUCCESS_EDIT:
                return sprintf("L'entité %s à bien été modifiée", $this->getClassName());
                break;
            default:
                throw new \RuntimeException();
                break;
        }
    }

    abstract protected function getClassName();

    /**
     * @param int $id
     * @Route("/{id}/display", requirements={"id","\d+"}, options={"expose"=true})
     * @Method("GET")
     *
     * @return Response
     */
    public function displayAction(int $id)
    {
        $object = $this->findById($id);

        return $this->render($this->makePath(self::VIEW_DISPLAY), ['object' => $object]);
    }

    /**
     * @param int $id
     *
     * @return object
     */
    protected function findById(int $id)
    {
        $object = $this->getEm()->find($this->getClass(), $id);
        if (null === $object) {
            throw $this->createNotFoundException($this->makePath(self::ERROR_NOTFOUND, $id));
        }

        return $object;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    abstract protected function getClass();

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/create")
     * @Method({"GET","POST"})
     */
    public function createAction(Request $request)
    {
        $object = $this->instance();
        $form   = $this->createForm($this->getForm(), $object);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getEm()->persist($object);
            $this->getEm()->flush();
            $this->addFlash('success', $this->makePath(self::SUCCESS_SAVE));

            return $this->redirectToRoute($this->makePath(self::ROUTE_DISPLAY), ['id' => $object->getId()]);
        }

        return $this->render(
            'ProjectBackBundle:Default:create.html.twig',
            [
                'form'   => $form->createView(),
                'action' => 'Créer un(e) '.$this->getClassName(),
            ]
        );
    }

    /**
     * @return object
     */
    protected function instance()
    {
        $r      = new \ReflectionClass($this->getClass());
        $object = $r->newInstance();
        if (null === $object) {
            throw $this->createNotFoundException("Unable instance");
        }

        return $object;
    }

    abstract protected function getForm();

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     * @Route("/{id}/edit", requirements={"id":"\d+"}, options={"expose"=true})
     * @Method({"GET","POST"})
     */
    public function editAction(Request $request, int $id)
    {
        $object = $this->findById($id);
        $form   = $this->createForm($this->getForm(), $object);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getEm()->flush();
            $this->addFlash('success', $this->makePath(self::SUCCESS_EDIT));

            return $this->redirectToRoute($this->makePath(self::ROUTE_DISPLAY), ['id' => $object->getId()]);
        }

        return $this->render(
            'ProjectBackBundle:Default:create.html.twig',
            [
                'form'   => $form->createView(),
                'action' => 'Modifier un(e)'.$this->getClassName(),
            ]
        );
    }

    /**
     * @param int    $id
     * @param string $token
     * @Route("/{id}/remove/{token}", requirements={"id":"\d+"})
     * @Method({"GET"})
     *
     * @return RedirectResponse
     */
    public function removeAction(int $id, string $token)
    {
        $object = $this->check($id, 'remove', $token);
        $this->getEm()->remove($object);
        $this->getEm()->flush();
        $this->addFlash('success', "object $id has been removed");

        return $this->redirectToRoute($this->makePath(self::ROUTE_INDEX));
    }

    /**
     * @param int    $id
     * @param string $key
     * @param string $token
     *
     * @return object
     */
    protected function check(int $id, string $key, string $token)
    {
        $object = $this->findById($id);
        $key    = mb_strtolower($this->getClassName()).'_'.$key.'_'.$object->getId();
        if (!$this->isCsrfTokenValid($key, $token)) {
            throw $this->createAccessDeniedException("Csrf error");
        }
        if ($this->getUser() === $object) {
            throw $this->createAccessDeniedException("Do you really want lock you !");
        }

        return $object;
    }
}
