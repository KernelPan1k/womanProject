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
use Project\FrontBundle\Entity\City;
use Project\FrontBundle\Entity\Context;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UserData
 * @package Project\BackBundle\DataFixtures\ORM
 */
class LoadUserData extends AbstractLoadData
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        parent::load($manager);

        $rootPath    = getcwd().'/src/Project/BackBundle/DataFixtures/datas/';
        $firstnames  = [];
        $lastnames   = [];
        $decriptions = [];

        $data  = file_get_contents($rootPath.'firstname.txt', true);
        $split = explode(',', $data);

        foreach ($split as $line) {
            if (!empty($line)) {
                $firstnames[] = $line;
            }
        }

        $data  = file_get_contents($rootPath.'lastname.txt', true);
        $split = explode(',', $data);

        foreach ($split as $line) {
            if (!empty($line)) {
                $lastnames[] = $line;
            }
        }

        $data  = file_get_contents($rootPath.'description.txt', true);
        $split = explode('&&&', $data);

        foreach ($split as $line) {
            if (!empty($line)) {
                $decriptions[] = $line;
            }
        }

        $max = min(count($lastnames), count($firstnames));

        $z  = 18;
        $c  = 1;
        $c2 = 3;
        $n  = 0;

        $imgName = 'last-member.jpg';
        $root = $this->container->get('kernel')->getRootDir();
        $imgRootPath = realpath($root . '/../src/Project/BackBundle/DataFixtures/datas/');
        $imgPath = "$imgRootPath/$imgName";
        $web = realpath($root . '/../web');
        chdir($web);

        for ($i = 0; $i < $max; $i++) {
            $z           = (70 > $z + $i) ? (round(($z + $i) / 20) + 20) : $z;
            $old         = $z + $i;
            $c           = ($c++ < 5) ? $c : 1;
            $c2          = ($c2++ < 5) ? $c2 : 1;
            $n           = ($n++ > 4) ? 0 : $n;
            $cp          = ((0 === $n) ? '75000' : ((1 === $n) ? '78000' : ((2 === $n) ? '1000' : '1110')));
            $ext         = ($n < 2) ? '.fr' : '.ch';
            $firstname = trim(mb_strtolower($firstnames[$i]));
            $lastname = trim(mb_strtolower($lastnames[$i]));
            $email       = $firstname.'@'.$lastname.$ext;
            $password    = $firstname.'$$'.$lastname;
            $username    = strrev($firstname).count($lastname);
            $dateOfBirth = new \DateTime("-$old year");
            $decription = $decriptions[mt_rand(0, (count($decriptions) - 1))];

            $categorys = $manager->getRepository(Category::class)->findByContext(Context::CONTEXT_USER);
            $category1 = $categorys[0];
            $category2 = $categorys[1];
            /** @var City $city */
            $city = $this->getReference("city-$cp");
            $user = new User();
            $user->setUsername($username);
            $user->setFirstName($firstname);
            $user->setLastName($lastname);
            $user->setEmail($email);
            $user->setPlainPassword($password);
            $user->setDateOfBirth($dateOfBirth);
            $user->setDescription($decription);
            $user->setCity($city);
            $user->addCategory($category1);
            $user->addCategory($category2);
            $user->setEnabled(true);
            $user->setLastLogin(new \DateTime("-$c day"));
            $dir = $user->getUploadDir() . ($i + 1);
            if (is_dir($dir) && is_dir($dir . '/avatar/')) {
                $files = glob($dir . '/avatar/*');
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
                rmdir($dir . '/avatar/');
                rmdir($dir);
            }
            $dest = tempnam(sys_get_temp_dir(), "user");
            copy($imgPath, $dest);
            $user->setFile(
                new UploadedFile($dest, $imgName, "image/jpg", filesize($dest), null, true)
            );
            $this->addReference('user-'.$i, $user);
            $manager->persist($user);
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
        return 30;
    }
}
