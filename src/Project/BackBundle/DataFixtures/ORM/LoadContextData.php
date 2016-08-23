<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 18.07.16
 * Time: 22:05
 */

namespace Project\BackBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Project\FrontBundle\Entity\Context;

/**
 * Class LoadContextData
 * @package Project\BackBundle\DataFixtures\ORM
 */
class LoadContextData extends AbstractLoadData
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        parent::load($manager);
        $contexts = [Context::CONTEXT_USER, Context::CONTEXT_NEWS, Context::CONTEXT_TAKEN, Context::CONTEXT_FORUM];
        foreach ($contexts as $context) {
            $object = new Context();
            $object->setContext($context);
            $manager->persist($object);
            $this->addReference('context-'.mb_strtolower($context), $object);
        }
        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return parent::getOrder();
    }
}
