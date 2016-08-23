<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 29.07.16
 * Time: 15:53
 */

namespace Project\BackBundle\DataFixtures\ORM;

use Application\UserBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Project\FrontBundle\Entity\ForumPost;
use Project\FrontBundle\Entity\PostResponse;

/**
 * Class LoadPostResponseData
 * @package Project\BackBundle\DataFixtures\ORM
 */
class LoadPostResponseData extends AbstractLoadData
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        parent::load($manager);
        $post = $manager->getRepository(ForumPost::class)->findAll();
        for ($p = 0; $p < count($post); $p++) {
            for ($i = 0; $i < 20; $i++) {
                /** @var User $user */
                $user   = $this->getReference("user-$i");
                $object = new PostResponse();
                $object->setPost($post[$p]);
                $object->setUser($user);
                $object->setMessage("Je pense que vous devriez ecrire $i");
                if (0 === $i % 3) {
                    $like = $i + 2 * 3;
                    $object->setLike($like);
                }
                $manager->persist($object);
                if (0 === $i % 5) {
                    for ($s = 0; $s < 4; $s++) {
                        $user      = $this->getReference("user-".($i + $s + 1));
                        $subObject = new PostResponse();
                        $subObject->setPost($post[$p]);
                        $subObject->setUser($user);
                        $subObject->setMessage("Vous avez raison $i");
                        $object->addChild($subObject);
                        $manager->persist($object);
                    }
                }
            }
        }
        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 60;
    }
}
