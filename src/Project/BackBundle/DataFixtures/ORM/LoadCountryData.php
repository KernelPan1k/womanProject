<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 25.06.16
 * Time: 22:07
 */

namespace Project\BackBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Project\FrontBundle\Entity\City;
use Project\FrontBundle\Entity\Country;
use Project\FrontBundle\Entity\Region;

/**
 * Class CategoryData
 * @package Project\BackBundle\DataFixtures\ORM
 */
class LoadCountryData extends AbstractLoadData
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        parent::load($manager);

        $rootPath = getcwd().'/src/Project/BackBundle/Command/datas/';
        $france   = $rootPath.'france';
        $swiss    = $rootPath.'swiss';
        $file     = $france;
        $content  = file_get_contents($file);
        $json     = json_decode($content, true);

        $country = new Country();
        $country->setName('France');

        $manager->persist($country);
        $manager->flush();

        foreach ($json as $canton => $values) {

            $ctn = new Region();
            $ctn->setCountry($country);
            $ctn->setName($canton);

            $manager->persist($ctn);

            foreach ($values as $datas) {

                $npa  = $datas['npa'];
                $reg  = $datas['canton'];
                $name = $datas['city'];

                if (0 < $npa && 5 === strlen($npa) && !empty($name) && $reg == $ctn->getName()) {

                    $city = new City();
                    $city->setName($name);
                    $city->setCp($npa);
                    $city->setRegion($ctn);

                    if ('paris' === strtolower($name) && '75000' === $npa
                        ||
                        'versailles' === strtolower($name) && '78000' === $npa
                    ) {

                        $this->addReference("city-$npa", $city);
                    }

                    $manager->persist($city);

                }

            }
        }

        $manager->flush();

        echo "\033[32m [OK] SUCCESS\033[0m\n";

        $file    = $swiss;
        $content = file_get_contents($file);
        $json    = json_decode($content, true);

        $country = new Country();
        $country->setName('Suisse');
        $manager->persist($country);
        $manager->flush();

        foreach ($json as $canton => $values) {


            $correspondance = [
                "VD" => "Vauds",
                "VS" => "Valais",
                "GE" => "Genève",
                "FR" => "Fribourg",
                "NE" => "Neuchâtel",
                "JU" => "Jura",
                "BE" => "Berne",
                "ZH" => "Zurich",
                "LU" => "Lucerne",
                "UR" => "Uri",
                "SZ" => "Schwytz",
                "OW" => "Obwald",
                "NW" => "Nidwald",
                "GL" => "Glaris",
                "ZG" => "Zoug",
                "SO" => "Soleure",
                "BS" => "Bâle-Ville",
                "BL" => "Bâle-Campagne",
                "SH" => "Schaffhouse",
                "AR" => "Appenzell Rhodes-Extérieures",
                "AI" => "Appenzell Rhodes-Intérieures",
                "SG" => "Saint-Gall",
                "GR" => "Grisons",
                "AG" => "Argovie",
                "TG" => "Thurgovie",
                "TI" => "Tessin",
            ];

            $region = new Region();
            $region->setCountry($country);
            $region->setAbv($canton);
            $region->setName($correspondance[$canton]);

            $manager->persist($region);

            foreach ($values as $datas) {

                $npa  = $datas['npa'];
                $reg  = $datas['canton'];
                $name = $datas['city'];

                if (0 < $npa && 4 === strlen($npa) && !empty($name) && $reg == $region->getAbv()) {

                    $city = new City();
                    $city->setName($name);
                    $city->setCp($npa);
                    $city->setRegion($region);

                    if ('lausanne' === strtolower($name) && '1000' === $npa
                        ||
                        'morges' === strtolower($name) && '1110' === $npa
                    ) {

                        $this->addReference("city-$npa", $city);
                    }

                    $manager->persist($city);

                }

            }
        }

        $manager->flush();
        echo "\033[32m [OK] SUCCESS\033[0m\n";


    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 20;
    }
}
