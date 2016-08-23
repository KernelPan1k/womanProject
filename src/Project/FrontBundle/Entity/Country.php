<?php

namespace Project\FrontBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\CountryRepository")
 */
class Country
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
     * @ORM\Column(name="name", type="string", length=40, unique=true)
     */
    private $name;

    /**
     * @var ArrayCollection|Region[]
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\Region", mappedBy="country")
     */
    private $regions;

    /**
     * Country constructor.
     */
    public function __construct()
    {
        $this->regions = new ArrayCollection();
    }

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
     * @return Country
     */
    public function setName(string $name): Country
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ArrayCollection|Region[]
     */
    public function getRegions()
    {

        return $this->regions;
    }

    /**
     * @param Region $region
     *
     * @return Country
     */
    public function addRegion(Region $region): Country
    {
        if (!$this->regions->contains($region)) {
            $region->setCountry($this);
            $this->regions->add($region);
        }

        return $this;
    }

    /**
     * @param Region $region
     */
    public function removeRegion(Region $region)
    {
        $this->regions->removeElement($region);
    }
}
