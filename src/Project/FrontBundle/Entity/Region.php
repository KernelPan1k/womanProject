<?php

namespace Project\FrontBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Region
 *
 * @ORM\Table(name="region")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\RegionRepository")
 */
class Region
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="Project\FrontBundle\Entity\Country", inversedBy="regions")
     */
    private $country;

    /**
     * @var string
     * @ORM\Column(name="abv", type="string", length=50, nullable=true)
     */
    private $abv;

    /**
     * @var ArrayCollection|City[]
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\City", mappedBy="region")
     */
    private $citys;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Region
     */
    public function setName(string $name): Region
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get country
     *
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set country
     *
     * @param Country $country
     *
     * @return Region
     */
    public function setCountry(Country $country): Region
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getAbv()
    {
        return $this->abv;
    }

    /**
     * @param string $abv
     *
     * @return Region
     */
    public function setAbv(string $abv): Region
    {
        $this->abv = $abv;

        return $this;
    }

    /**
     * @return ArrayCollection|City[]
     */
    public function getCitys()
    {

        return $this->citys;
    }

    /**
     * @param City $city
     *
     * @return Region
     */
    public function addCity(City $city): Region
    {
        if (!$this->citys->contains($city)) {
            $city->setRegion($this);
            $this->citys->add($city);
        }

        return $this;
    }

    /**
     * @param City $city
     */
    public function removeCity(City $city)
    {
        $this->citys->removeElement($city);
    }
}
