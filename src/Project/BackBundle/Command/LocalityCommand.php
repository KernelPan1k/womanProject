<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 25.06.16
 * Time: 06:43
 */

namespace Project\BackBundle\Command;

use Project\FrontBundle\Entity\City;
use Project\FrontBundle\Entity\Country;
use Project\FrontBundle\Entity\Region;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class LocalityCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('project_admin:locality:update')
            ->setDescription('Add all locality by country');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs       = new Filesystem();
        $rootPath = getcwd().'/src/Project/BackBundle/Command/datas/';
        $manager  = $this->getContainer()->get('doctrine')->getManager();
        $sql      =
            'START TRANSACTION;
            SET FOREIGN_KEY_CHECKS=0;
            TRUNCATE city;
            TRUNCATE region;
            TRUNCATE country;
            SET FOREIGN_KEY_CHECKS=1;
            COMMIT;';

        echo "\033[32m [OK] TRUNCATE\033[0m\n";
        $manager->getConnection()->query($sql);
        $france = $rootPath.'france';
        $swiss  = $rootPath.'swiss';

        if ($fs->exists($france)) {
            $file    = $france;
            $content = file_get_contents($file);
            $json    = json_decode($content, true);

            $nbr = (int)(count($json, 1) / 4);

            $country = new Country();
            $country->setName('France');

            $manager->persist($country);
            $manager->flush();

            $progress = new ProgressBar($output, $nbr);

            $progress->start();

            $i = 0;

            foreach ($json as $canton => $values) {

                $i++;

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

                        $manager->persist($city);

                    }

                    $progress->advance();
                }
            }

            $manager->flush();
            $progress->finish();

            echo "\033[32m [OK] SUCCESS\033[0m\n";


        }

        if ($fs->exists($swiss)) {

            $file    = $swiss;
            $content = file_get_contents($file);
            $json    = json_decode($content, true);

            $nbr = (int)(count($json, 1) / 4);

            $country = new Country();
            $country->setName('Suisse');
            $manager->persist($country);
            $manager->flush();

            $progress = new ProgressBar($output, $nbr);

            $progress->start();

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

                        $manager->persist($city);

                    }

                    $progress->advance();
                }
            }

            $progress->finish();
            $manager->flush();

            echo "\033[32m [OK] SUCCESS\033[0m\n";

        } else {
            echo "Error parameter :\nlocality:update france|swiss ";
        }
    }
}
