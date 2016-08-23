<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 19.07.16
 * Time: 05:40
 */

namespace Project\BackBundle\Controller;

use Project\FrontBundle\Entity\Category;
use Project\FrontBundle\Form\Admin\CategoryAdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class CategoryController
 * @package Project\BackBundle\Controller
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Route("/category")
 */
class CategoryController extends AbstractCRUDController implements BackControllerInterface
{

    public function getClassName()
    {
        return 'Category';
    }

    public function getClass()
    {
        return Category::class;
    }

    public function getForm()
    {
        return CategoryAdminType::class;
    }
}
