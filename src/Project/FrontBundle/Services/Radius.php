<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 29.06.16
 * Time: 06:33
 */

namespace Project\FrontBundle\Services;

use Craue\GeoBundle\Entity\GeoPostalCode;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Radius
 * @package Project\FrontBundle\Services
 */
class Radius
{
    /** @var EntityManagerInterface $managerInterface */
    private $managerInterface;

    /**
     * Radius constructor.
     *
     * @param EntityManagerInterface $managerInterface
     */
    public function __construct(EntityManagerInterface $managerInterface)
    {
        $this->managerInterface = $managerInterface;
    }

    public function radius(int $km, string $zip, string $country)
    {

        $sql          =
            'poi.postalCode, 
            GEO_DISTANCE_BY_POSTAL_CODE(:country, :postalCode, poi.country, poi.postalCode) AS HIDDEN distance';
        $queryBuilder =
            $this->managerInterface->createQueryBuilder();
        $queryBuilder
            ->select($sql)
            ->from(GeoPostalCode::class, 'poi')
            ->having('distance <= :radius')
            ->setParameter('country', $country)
            ->setParameter('postalCode', $zip)
            ->setParameter('radius', $km)
            ->orderBy('distance');

        $results = $queryBuilder->getQuery()->getArrayResult();
        $result  = array_column($results, 'postalCode');

        return $result;
    }
}
