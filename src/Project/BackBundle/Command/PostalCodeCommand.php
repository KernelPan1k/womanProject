<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 25.06.16
 * Time: 06:17
 */

namespace Project\BackBundle\Command;

use Craue\GeoBundle\Entity\GeoPostalCode;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MyGeonamesPostalCodeData
 * @package Project\BackBundle\Fixtures
 */
class PostalCodeCommand extends ContainerAwareCommand
{


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('project_admin:geopostalcode:update')
            ->setDescription('Add all locality by country');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $batchSize = 1000;
        $manager   = $this->getContainer()->get('doctrine')->getManager();
        $sql       =
            'START TRANSACTION;
            SET FOREIGN_KEY_CHECKS=0;
            TRUNCATE craue_geo_postalcode;
            SET FOREIGN_KEY_CHECKS=1;
            COMMIT;';

        $manager->getConnection()->query($sql);

        echo "\033[32m [OK] TRUNCATE\033[0m\n";

        $repo = $manager->getRepository(GeoPostalCode::class);

        $basepath  = getcwd().'/src/Project/BackBundle/Command/datas/';
        $filenames = [$basepath.'CH.txt', $basepath.'FR.txt'];

        foreach ($filenames as $filename) {

            $entriesAdded        = 0;
            $currentBatchEntries = [];

            $fcontents = file($filename);
            for ($i = 0, $numLines = count($fcontents); $i < $numLines; ++$i) {
                $line = trim($fcontents[$i]);
                $arr  = explode("\t", $line);

                // skip if no lat/lng values
                if (!array_key_exists(9, $arr) || !array_key_exists(10, $arr)) {
                    continue;
                }

                $country = $arr[0];

                if ($basepath.'FR.txt' === $filename) {
                    $postalCode = substr($arr[1], 0, 5);
                    if (!preg_match('/^[0-9]{5}$/', $postalCode)) {
                        echo "Error\n";
                        continue;
                    }
                } else {
                    $postalCode = substr($arr[1], 0, 4);
                    if (!preg_match('/^[0-9]{4}$/', $postalCode)) {
                        echo "Error\n";
                        continue;
                    }
                }

                // skip duplicate entries in current batch
                if (in_array($country.'-'.$postalCode, $currentBatchEntries)) {
                    continue;
                }

                // skip duplicate entries already persisted
                if ($repo->findOneBy(['country' => $country, 'postalCode' => $postalCode]) !== null) {
                    continue;
                }

                $entity = new GeoPostalCode();
                $entity->setCountry($country);
                $entity->setPostalCode($postalCode);
                $entity->setLat((float)$arr[9]);
                $entity->setLng((float)$arr[10]);
                $manager->persist($entity);

                ++$entriesAdded;
                $currentBatchEntries[] = $country.'-'.$postalCode;

                if ((($i + 1) % $batchSize) === 0) {
                    $manager->flush();
                    $manager->clear();
                    $currentBatchEntries = [];
                    echo '.'; // progress indicator
                }
            }
        }

        $manager->flush();

        echo ' OK ', "\n";
    }
}
