<?php

namespace Project\FrontBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
    public function findByContext(string $context)
    {
        $sql   = "SELECT c FROM %s c JOIN c.contexts t WHERE t.context = :context";
        $query = $this->_em->createQuery(sprintf($sql, $this->getClassName()))->setParameter('context', $context);

        return $query->getResult();
    }
}
