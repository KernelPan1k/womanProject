<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 19.07.16
 * Time: 05:40
 */

namespace Project\BackBundle\Controller;

use Project\FrontBundle\Entity\Taken;
use Project\FrontBundle\Form\Admin\TakenAdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class TakenController
 * @package Project\BackBundle\Controller
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Route("/taken")
 */
class TakenController extends AbstractCRUDController implements BackControllerInterface
{

    public function getClassName()
    {
        return 'Taken';
    }

    public function getClass()
    {
        return Taken::class;
    }

    public function getForm()
    {
        return TakenAdminType::class;
    }
}
