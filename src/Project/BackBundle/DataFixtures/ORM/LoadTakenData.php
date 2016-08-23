<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 22.07.16
 * Time: 20:55
 */

namespace Project\BackBundle\DataFixtures\ORM;

use Application\UserBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Project\FrontBundle\Entity\Category;
use Project\FrontBundle\Entity\City;
use Project\FrontBundle\Entity\Context;
use Project\FrontBundle\Entity\Taken;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class LoadTakenData
 * @package Project\BackBundle\DataFixtures\ORM
 */
class LoadTakenData extends AbstractLoadData
{

    private $descrition = "Quam ob rem id primum videamus, si placet, quatenus amor in amicitia progredi debeat. Numne, si Coriolanus habuit amicos, ferre contra patriam arma illi cum Coriolano debuerunt? num Vecellinum amici regnum adpetentem, num Maelium debuerunt iuvare? \n Quam ob rem id primum videamus, si placet, quatenus amor in amicitia progredi debeat. Numne, si Coriolanus habuit amicos, ferre contra patriam arma illi cum Coriolano debuerunt? num Vecellinum amici regnum adpetentem, num Maelium debuerunt iuvare?";

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        parent::load($manager);
        $categorys = $manager->getRepository(Category::class)->findByContext(Context::CONTEXT_TAKEN);
        $porrentry = $manager->getRepository(City::class)->findOneBy(['name' => 'Porrentruy', 'cp' => 2900]);
        $lausanne  = $manager->getRepository(City::class)->findOneBy(['name' => 'Lausanne', 'cp' => 1000]);
        /** @var City $citys */
        $citys       = [$porrentry, $lausanne];
        $c           = 0;
        $imgName     = 'forum-beauty.jpg';
        $root        = $this->container->get('kernel')->getRootDir();
        $imgRootPath = realpath($root.'/../src/Project/BackBundle/DataFixtures/datas/');
        $imgPath     = "$imgRootPath/$imgName";
        $web         = realpath($root.'/../web');
        chdir($web);
        for ($i = 0; $i < 30; $i++) {
            /** @var User $user */
            $user   = $this->getReference("user-$i");
            $c      = ++$c < count($categorys) ? $c : 0;
            $v      = (0 === $i % 2) ? 0 : 1;
            $object = new Taken();
            $object->setTitle("Un titre de sortie");
            $object->setDescription($this->descrition);
            $object->setCategory($categorys[$c]);
            $object->setCity($citys[$v]);
            $object->setUser($user);
            $object->setCreatedAt(new \DateTime("-15 day"));
            $start = new \DateTime('+3 day');
            $end   = new \DateTime('+3 day');
            $start->setTime(10, 00, 00);
            $end->setTime(11, 00, 00);
            $object->setStartDate($start);
            $object->setEndDate($end);
            $object->setNbrPerson($i + 5);
            $object->setPrice($i + 20);
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
            $dest = tempnam(sys_get_temp_dir(), "taken");
            copy($imgPath, $dest);
            $object->setFile(
                new UploadedFile($dest, $imgName, "image/jpg", filesize($dest), null, true)
            );
            $object->setAlt('alt');
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
