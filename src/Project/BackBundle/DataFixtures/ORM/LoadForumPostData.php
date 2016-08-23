<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 29.07.16
 * Time: 15:32
 */

namespace Project\BackBundle\DataFixtures\ORM;

use Application\UserBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Project\FrontBundle\Entity\Forum;
use Project\FrontBundle\Entity\ForumPost;

/**
 * Class LoadForumPostData
 * @package Project\BackBundle\DataFixtures\ORM
 */
class LoadForumPostData extends AbstractLoadData
{

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        parent::load($manager);
        $content = "Je suis le contenu de la news %d. Get the order of this fixture, Get the order of this fixture. Get the order of this fixtureGet the order of this fixtureGet the order of this fixture";
        $forums = $manager->getRepository(Forum::class)->findAll();
        for ($f = 0; $f < count($forums); $f++) {
            for ($i = 0; $i < 50; $i++) {
                /** @var User $user */
                $user = $this->getReference("user-$i");
                $object = new ForumPost();
                $object->setForum($forums[$f]);
                $object->setTitle("Pourquoi j'aimerais savoir ou pas $i ?");
                $object->setDescription($content);
                $object->setUser($user);
                $manager->persist($object);
            }
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 50;
    }
}
