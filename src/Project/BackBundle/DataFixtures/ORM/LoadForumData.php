<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 26.07.16
 * Time: 23:21
 */

namespace Project\BackBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Project\FrontBundle\Entity\Category;
use Project\FrontBundle\Entity\Context;
use Project\FrontBundle\Entity\Forum;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class LoadForumData
 * @package Project\BackBundle\DataFixtures\ORM
 */
class LoadForumData extends AbstractLoadData
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        parent::load($manager);
        $category    = $manager->getRepository(Category::class)->findByContext(Context::CONTEXT_FORUM);
        $imgName     = 'forum-beauty.jpg';
        $root        = $this->container->get('kernel')->getRootDir();
        $imgRootPath = realpath($root.'/../src/Project/BackBundle/DataFixtures/datas/');
        $imgPath     = "$imgRootPath/$imgName";
        $web         = realpath($root.'/../web');
        chdir($web);
        for ($i = 0; $i < count($category); $i++) {
            $object = new Forum();
            $object->setCategory($category[$i]);
            $object->setName("Forum $i");
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
            $dest = tempnam(sys_get_temp_dir(), "forum");
            copy($imgPath, $dest);
            $object->setFile(
                new UploadedFile($dest, $imgName, "image/jpg", filesize($dest), null, true)
            );
            $object->setAlt('alt');
            $manager->persist($object);
            $this->addReference('forum-'.$i, $object);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 40;
    }
}
