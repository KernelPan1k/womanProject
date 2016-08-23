<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 25.06.16
 * Time: 22:07
 */

namespace Project\BackBundle\DataFixtures\ORM;

use Application\UserBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Project\FrontBundle\Entity\Category;
use Project\FrontBundle\Entity\News;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class LoadNewsData
 * @package Project\BackBundle\DataFixtures\ORM
 */
class LoadNewsData extends AbstractLoadData
{

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        parent::load($manager);
        $title   = [
            "News 1",
            "News 2",
            "News 3",
            "News 4",
        ];
        $teaser  = "je suis le teaser de la news %d";
        $content = "Je suis le contenu de la news %d. Get the order of this fixture, Get the order of this fixture. Get the order of this fixtureGet the order of this fixtureGet the order of this fixture";
        /** @var User $user */
        $user = $this->getReference("user-1");

        $imgName     = 'forum-beauty.jpg';
        $root        = $this->container->get('kernel')->getRootDir();
        $imgRootPath = realpath($root.'/../src/Project/BackBundle/DataFixtures/datas/');
        $imgPath     = "$imgRootPath/$imgName";
        $web         = realpath($root.'/../web');
        chdir($web);

        for ($i = 0; $i < count($title); $i++) {
            $cpt    = $i + 1;
            $object = new News();
            $object->setTitle(sprintf($title[$i], $i));
            $object->setTeaser(sprintf($teaser, $i));
            $object->setContent(sprintf($content, $i));
            /** @var Category $cat */
            $cat = $this->getReference('category-'.$cpt);
            $object->setCategory($cat);
            $object->setAuthor($user);
            $object->setCreatedAt(new \DateTime("-$cpt month"));
            $dir = $object->getUploadDir().($i + 1);
            if (is_dir($dir)) {
                $files = glob($dir.'/*');
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
                rmdir($dir);
            }
            $dest = tempnam(sys_get_temp_dir(), "news");
            copy($imgPath, $dest);
            $object->setFile(
                new UploadedFile($dest, $imgName, "image/jpg", filesize($dest), null, true)
            );
            $object->setAlt($title[$i]);
            $object->setEnabled(true);
            $manager->persist($object);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 40;
    }
}
