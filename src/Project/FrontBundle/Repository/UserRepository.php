<?php

namespace Project\FrontBundle\Repository;

use Application\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class UserRepository
 * @package Project\FrontBundle\Repository
 */
class UserRepository extends EntityRepository implements AjaxSearchInterface
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function findRandByCity(User $user)
    {
        $dql = $this->baseRand();
        $dql .= "AND u.city = :city AND u NOT IN(:user) ORDER BY rand";
        $query = $this->_em->createQuery($dql);
        $query->setParameter('city', $user->getCity());
        $query->setParameter('user', $user);
        $query->setMaxResults(5);

        return $query->getResult();
    }

    /**
     * @return string
     */
    private function baseRand()
    {
        $sql =
            "SELECT DISTINCT u, rand() as HIDDEN rand FROM %s u 
            JOIN u.categorys cat 
            JOIN u.city city 
            WHERE u.enabled = true 
            AND u.locked = false 
            AND u.lastLogin IS NOT NULL 
            AND u.description IS NOT NULL ";

        return sprintf($sql, User::class);
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function findRandByCategorys(User $user)
    {
        $dql = $this->baseRand();
        $dql .= "AND cat IN(:cats) AND u NOT IN(:user) ORDER BY rand";
        $query = $this->_em->createQuery($dql);
        $query->setParameter('cats', $user->getCategorys());
        $query->setParameter('user', $user);
        $query->setMaxResults(5);

        return $query->getResult();
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function findRandByOld(User $user)
    {
        $old = $user->getDateOfBirth();
        $min = clone $old;
        $max = clone $old;
        $min->modify('-10 year');
        $max->modify('+10 year');

        $dql = $this->baseRand();
        $dql .= "AND u.dateOfBirth BETWEEN :min AND :max AND u NOT IN(:user) ORDER BY rand";
        $query = $this->_em->createQuery($dql);
        $query->setParameter('min', $min);
        $query->setParameter('max', $max);
        $query->setParameter('user', $user);
        $query->setMaxResults(5);

        return $query->getResult();
    }

    public function findComplete()
    {
        $sql =
            "SELECT DISTINCT u FROM %s u 
            JOIN u.categorys cat 
            JOIN u.city city 
            WHERE u.enabled = true 
            AND u.locked = false 
            AND u.lastLogin IS NOT NULL 
            AND u.description IS NOT NULL ";

        $dql   = sprintf($sql, $this->getClassName());
        $query = $this->_em->createQuery($dql);

        return $query->getResult();
    }

    /**
     * @param      $terms
     *
     * @return QueryBuilder
     */
    public function search($terms)
    {
        $qb = $this->createQueryBuilder("user");

        if (is_numeric($terms)) {
            $qb->where("user.id= :id");
            $qb->setParameter("id", $terms);

            return $qb;
        }

        foreach (explode(" ", $terms) as $index => $term) {
            $qb->orWhere("user.username like :term");
            if (is_numeric($term)) {
                $qb->orWhere("user.id = :id");
                $qb->setParameter("id", $term);
            }
            $qb->setParameter("term", "%".$term."%");
        }

        return $qb->getQuery();
    }
}
