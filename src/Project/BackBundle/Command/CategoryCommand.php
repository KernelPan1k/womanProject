<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 25.06.16
 * Time: 06:43
 */

namespace Project\BackBundle\Command;

use Project\FrontBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CategoryCommand extends ContainerAwareCommand
{

    private $list = [
        'Sport',
        'Cuisine',
        'Art créatif',
        'Esotérisme',
        'Jeux',
        'Sorties',
        'Famille',
        'Argent',
        'Hight tech',
        'Travail',
        'Shopping',
        'Mode',
        'Beauté',
        'Santé',
        'Sexe',
        'Psychologie',
        'Amour',
        'Minceur',
        'Décoration',
        'Culture',
        'Voyage',
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('project_admin:category:update')
            ->setDescription('Add all category');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $manager = $this->getContainer()->get('doctrine')->getManager();
        $sql     =
            'START TRANSACTION;
            SET FOREIGN_KEY_CHECKS=0;
            TRUNCATE category;
            SET FOREIGN_KEY_CHECKS=1;
            COMMIT;';

        echo "\033[32m [OK] TRUNCATE\033[0m\n";
        $manager->getConnection()->query($sql);

        foreach ($this->list as $l) {
            $category = new Category();
            $category->setName($l);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
