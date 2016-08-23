<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 29.06.16
 * Time: 04:31
 */

namespace Project\FrontBundle\Services;

use Application\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Project\FrontBundle\Entity\City;
use Project\FrontBundle\Entity\Region;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class NetworkSearch
 * @package Project\FrontBundle\Services
 */
class NetworkSearch
{
    const CITY = 0;
    const REGION = 1;

    /** @var EntityManagerInterface $managerInterface */
    private $managerInterface;
    /** @var Radius $radius */
    private $radius;
    /** @var TokenStorageInterface $storageInterface */
    private $storageInterface;

    /** @var  ArrayCollection|null $categorys */
    private $categorys;
    /** @var  City|null $city */
    private $city;
    /** @var  integer $type */
    private $type;
    /** @var  Region|null $region */
    private $region;
    /** @var  integer $min |null */
    private $min;
    /** @var  integer|null $max */
    private $max;
    /** @var  integer|null $distance */
    private $distance;
    /** @var  array $search */
    private $search;
    /** @var array $stopword */
    private $stopword;


    /**
     * NetworkSearch constructor.
     *
     * @param EntityManagerInterface $managerInterface
     * @param Stopword               $stopword
     * @param Radius                 $radius
     * @param TokenStorageInterface  $storageInterface
     */
    public function __construct(
        EntityManagerInterface $managerInterface,
        Stopword $stopword,
        Radius $radius,
        TokenStorageInterface $storageInterface
    ) {
        $this->managerInterface = $managerInterface;
        $this->stopword         = $stopword->getStopwords();
        $this->radius           = $radius;
        $this->storageInterface = $storageInterface;
    }

    public function search(array $datas)
    {
        foreach ($datas as $k => $v) {
            switch ($k) {
                case 'categorys':
                    $this->setCategorys($v);
                    break;
                case 'city':
                    $this->setCity($v);
                    break;
                case 'type':
                    $this->setType($v);
                    break;
                case 'region':
                    $this->setRegion($v);
                    break;
                case 'ageMin':
                    $this->setMin($v);
                    break;
                case 'ageMax':
                    $this->setMax($v);
                    break;
                case 'distance':
                    $this->setDistance($v);
                    break;
                case 'search':
                    $this->setSearch($v);
                    break;
                default:
                    continue;
            }
        }

        if (null !== $this->min && null !== $this->max && $this->min > $this->max) {
            $min       = $this->min;
            $max       = $this->max;
            $this->max = $min;
            $this->min = $max;
        }

        return $result = $this->query();
    }

    /**
     * @param null|ArrayCollection $categorys
     */
    private function setCategorys(ArrayCollection $categorys = null)
    {
        $this->categorys = $categorys;
    }

    /**
     * @param null|City $city
     */
    private function setCity(City $city = null)
    {
        $this->city = $city;
    }

    /**
     * @param int $type
     */
    private function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * @param null|Region $region
     */
    private function setRegion(Region $region = null)
    {
        $this->region = $region;
    }

    /**
     * @param int|null $min
     */
    private function setMin(int $min = null)
    {
        $this->min = $min;
    }

    /**
     * @param int|null $max
     */
    private function setMax(int $max = null)
    {
        $this->max = $max;
    }

    /**
     * @param int|null $distance
     */
    private function setDistance(int $distance = null)
    {
        $this->distance = $distance;
    }

    /**
     * @param string|null $search
     */
    private function setSearch(string $search = null)
    {
        $results = [];
        $search  = str_replace(',', ' ', $search);

        if (preg_match_all('/["]{1}([^"]+[^"]+)+["]{1}/i', $search, $betweenQuote)) {
            foreach ($betweenQuote[1] as $expression) {
                $results[] = $expression;
            }
            $anyExpressions = str_ireplace($betweenQuote[0], "", $search);
            $simpleWord     = explode(" ", $anyExpressions);
            $totalResults   = array_merge($betweenQuote[0], $simpleWord);
        } else {
            $totalResults = explode(" ", $search);
        }
        foreach ($totalResults as $key => $value) {
            if (strlen(trim($value)) <= 2) {
                $value = '';
            }
            if (!empty($this->stopWords)) {
                if (in_array($value, $this->stopWords)) {
                    $value = '';
                }
            }
            if (empty(trim($value))) {
                unset($totalResults[$key]);
            }
        }

        $this->search = $totalResults;
    }

    private function query()
    {
        $dql = "SELECT DISTINCT u, cat, city, region FROM %s u 
                JOIN u.categorys cat 
                JOIN u.city city 
                JOIN city.region region ";

        $schema = [];
        $vars   = [];
        $sql    = sprintf($dql, User::class);
        $where  =
            'WHERE u.enabled = true 
            AND u.locked = false 
            AND u.lastLogin IS NOT NULL 
            AND u.description IS NOT NULL ';

        if (null !== $this->categorys && !$this->categorys->isEmpty()) {
            $where .= "AND cat IN(:categorys) ";
            $schema[]          = 'categorys';
            $vars['categorys'] = ['categorys' => $this->categorys];
        }

        if (self::REGION === $this->type && null !== $this->region) {
            $where .= "AND region IN(:region) ";
            $schema[]       = 'region';
            $vars['region'] = ['region' => $this->region];

        } elseif (self::CITY === $this->type && null !== $this->city) {
            if (null !== $this->distance && 0 < $this->distance) {
                $zip     = $this->city->getCp();
                $country = $this->city->getRegion()->getCountry()->getName();
                $country = ('france' === mb_strtolower($country)) ? 'FR' : 'CH';
                $radius  = $this->radius->radius($this->distance, $zip, $country);
                $where .= 'AND city.cp IN(:radius) ';
                $schema[]       = 'radius';
                $vars['radius'] = ['radius' => $radius];
            } else {
                $where .= 'AND city IN(:city) ';
                $schema[]     = 'city';
                $vars['city'] = ['city' => $this->city];
            }
        }

        if (null !== $this->min || null !== $this->max) {
            if (null !== $this->min && null !== $this->max && $this->min === $this->max) {
                $mn = $this->min;
                $mn = (intval($mn) <= 18) ? 0 : $mn;
                if (0 !== $mn) {
                    $mx  = $mn + 1;
                    $min = new \DateTime("-$mn year");
                    $max = new \DateTime("-$mx year");
                    $where .= "AND u.dateOfBirth BETWEEN :max AND :min ";
                    $schema[]      = 'equal';
                    $vars['equal'] = ['min' => $min, 'max' => $max];
                }

            } else {
                if (null !== $this->min) {
                    $min = $this->min;
                    $min = (intval($min) <= 18) ? 0 : $min;
                    if (0 !== $min) {
                        $dateMin = new \DateTime("-$min year");
                        $where .= 'AND u.dateOfBirth <= :min ';
                        $schema[]    = 'min';
                        $vars['min'] = ['min' => $dateMin];
                    }
                }

                if (null !== $this->max) {
                    $max = $this->max;
                    $max = (intval($max) <= 18) ? 0 : $max;
                    if (0 !== $max) {
                        $dateMax = new \DateTime("-$max year");
                        $where .= 'AND u.dateOfBirth >= :max ';
                        $schema[]    = 'max';
                        $vars['max'] = ['max' => $dateMax];
                    }
                }
            }
        }

        if (null !== $this->search && 0 < count($this->search)) {
            $search = $this->search;
            $where .= "AND (";
            $i = 0;
            $v = [];
            foreach ($search as $s) {
                if (0 !== $i) {
                    $where .= "OR u.username LIKE CONCAT('%', :search$i, '%') ";
                    $where .= "OR u.description LIKE CONCAT('%', :search$i, '%') ";
                } else {
                    $schema[] = 'search';
                    $where .= "u.username LIKE CONCAT('%', :search$i, '%') ";
                    $where .= "OR u.description LIKE CONCAT('%', :search$i, '%') ";
                }
                $v ["search$i"] = $s;
                $i++;
            }
            $where .= ") ";
            $vars['search'] = $v;
        }

        $withOut = $this->useWithout();

        if (null !== $withOut && $withOut instanceof User) {

            $where .= 'AND u NOT IN(:without) ';
            $schema[]        = 'without';
            $vars['without'] = ['without' => $withOut];
        }

        $dql = $sql.$where."ORDER BY u.lastLogin DESC, u.updatedAt DESC";

        $query = $this->managerInterface->createQuery($dql);

        foreach ($schema as $s) {
            $var = $vars[$s];
            foreach ($var as $k => $v) {
                $query->setParameter($k, $v);
            }
        }

        return $query->getResult();
    }

    private function useWithout()
    {
        $user = $this->storageInterface->getToken()->getUser();

        return (null !== $user && $user instanceof User) ? $user : null;
    }
}
