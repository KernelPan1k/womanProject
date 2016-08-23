<?php

namespace Project\FrontBundle\Entity;

use Application\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\CityRepository")
 */
class City
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
     * @var Region
     *
     * @ORM\ManyToOne(targetEntity="Project\FrontBundle\Entity\Region", inversedBy="citys")
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="cp", type="string", length=5)
     */
    private $cp;

    /**
     * @var ArrayCollection|User[]
     * @ORM\OneToMany(targetEntity="Application\UserBundle\Entity\User", mappedBy="city")
     */
    private $users;
    /**
     * @var ArrayCollection|Taken[]
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\Taken", mappedBy="city")
     */
    private $takens;

    /**
     * City constructor.
     */
    public function __construct()
    {
        $this->users  = new  ArrayCollection();
        $this->takens = new ArrayCollection();
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
     * @return City
     */
    public function setName(string $name): City
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get region
     *
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set region
     *
     * @param Region $region
     *
     * @return City
     */
    public function setRegion(Region $region): City
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get cp
     *
     * @return string
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set cp
     *
     * @param string $cp
     *
     * @return City
     */
    public function setCp(string $cp): City
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * @return ArrayCollection|Taken[]
     */
    public function getTakens()
    {
        return $this->takens;
    }

    /**
     * @param Taken $taken
     *
     * @return City
     */
    public function addTaken(Taken $taken)
    {
        if (!$this->takens->contains($taken)) {
            $this->takens->add($taken);
            $taken->setCity($this);
        }

        return $this;
    }

    /**
     * @param Taken $taken
     */
    public function removeTaken(Taken $taken)
    {
        $this->takens->removeElement($taken);
    }

    /**
     * @return ArrayCollection|User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $user
     *
     * @return City
     */
    public function addUsers(User $user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCity($this);
        }

        return $this;
    }

    /**
     * @param User $user
     */
    public function removeUsers(User $user)
    {
        $this->users->removeElement($user);
    }
}
