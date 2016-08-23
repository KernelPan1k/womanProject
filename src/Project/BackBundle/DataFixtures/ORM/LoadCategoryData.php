<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 25.06.16
 * Time: 22:07
 */

namespace Project\BackBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Project\FrontBundle\Entity\Category;

/**
 * Class CategoryData
 * @package Project\BackBundle\DataFixtures\ORM
 */
class LoadCategoryData extends AbstractLoadData
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        parent::load($manager);
        $list = [
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

        $i             = 0;
        $context_users = $this->getReference('context-user');
        $context_news  = $this->getReference('context-news');
        $context_taken = $this->getReference('context-taken');
        $context_forum = $this->getReference('context-forum');
        $contexts      = [$context_users, $context_news, $context_taken, $context_forum];
        $c             = 0;
        foreach ($list as $l) {
            $c        = ++$c >= count($contexts) ? 0 : $c;
            $category = new Category();
            $category->setName($l);
            $category->addContext($contexts[$c]);
            $manager->persist($category);
            if (5 >= ++$i) {
                $this->addReference("category-$i", $category);
            }
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
        return 20;
    }
}
