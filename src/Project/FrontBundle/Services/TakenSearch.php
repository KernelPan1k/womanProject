<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 25.07.16
 * Time: 19:49
 */

namespace Project\FrontBundle\Services;

use Application\UserBundle\Entity\User;
use Doctrine\ORM\Query;
use Project\FrontBundle\Entity\City;
use Project\FrontBundle\Entity\Region;
use Project\FrontBundle\Entity\Taken;
use Project\FrontBundle\Repository\TakenRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class TakenSearch
 * @package Project\FrontBundle\Services
 */
class TakenSearch
{
    /** @var TokenStorageInterface $tokenStorage */
    private $tokenStorage;
    /** @var Radius $radius */
    private $radius;
    /** @var null|City */
    private $city = null;
    /** @var null|Region */
    private $region = null;
    /** @var int|null */
    private $distance = null;
    /** @var RegistryInterface $registry */
    private $registry;

    /**
     * TakenSearch constructor.
     *
     * @param Radius                $radius
     * @param TokenStorageInterface $tokenStorage
     * @param RegistryInterface     $registry
     */
    public function __construct(Radius $radius, TokenStorageInterface $tokenStorage, RegistryInterface $registry)
    {
        $this->tokenStorage = $tokenStorage;
        $this->radius       = $radius;
        $this->registry     = $registry;
    }

    /**
     * @param array $params
     */
    public function init(array $params)
    {
        foreach ($params as $key => $value) {
            switch ($key) {
                case 'distance':
                    $this->setDistance($value);
                    break;
                case 'search':
                    $this->setObject($value);
                    break;
                default:
                    continue;
            }
        }
    }

    /**
     * @param int $distance
     */
    private function setDistance(int $distance)
    {
        $kil            = intval($distance);
        $this->distance = (null !== $kil && 0 < $kil) ? $kil : null;
    }

    /**
     * @param City|Region $value
     */
    private function setObject($value)
    {
        $user = $this->getUser();
        if ($value instanceof City) {
            $this->city = $value;
        } elseif ($value instanceof Region) {
            $this->region = $value;
        } elseif (null !== $user && null !== $user->getCity()) {
            $this->city = $user->getCity();
        }
    }

    /**
     * @return User|null
     */
    private function getUser()
    {
        $user = $this->tokenStorage->getToken()->getUser();
        if ($user instanceof User) {
            return $user;
        }

        return null;
    }

    /**
     * @return Query
     */
    public function search()
    {
        $user     = $this->getUser();
        $city     = $this->city;
        $region   = $this->region;
        $distance = $this->distance;
        if (null !== $user) {
            if (null !== $city && null !== $distance) {
                $abr    = ('france' === mb_strtolower($city->getRegion()->getCountry()->getName())) ? 'FR' : 'CH';
                $radius = $this->radius->radius($distance, $city->getCp(), $abr);

                return $this->getRepo()->findByRadius($radius);
            } elseif (null !== $city && null === $this->distance) {
                return $this->getRepo()->findByCity($city);
            }
        } elseif (null !== $region) {
            return $this->getRepo()->findByRegion($region);
        }

        return $this->getRepo()->findTaken();
    }

    /**
     * @return TakenRepository
     */
    private function getRepo()
    {
        return $this->registry->getRepository(Taken::class);
    }
}
