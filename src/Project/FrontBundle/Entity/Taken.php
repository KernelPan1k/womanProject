<?php

namespace Project\FrontBundle\Entity;

use Application\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Project\FrontBundle\EntityTrait\UpdatedAtTrait;
use Project\FrontBundle\UploadFactory\UploadableInterface;
use Project\FrontBundle\UploadFactory\UploadableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Taken
 *
 * @ORM\Table(name="taken")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\TakenRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Taken implements UploadableInterface
{

    use UpdatedAtTrait;
    use UploadableTrait;

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
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Project\FrontBundle\Entity\Category",  inversedBy="takens")
     * @Assert\NotBlank()
     */
    private $category;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="Project\FrontBundle\Entity\City", inversedBy="takens")
     * @Assert\NotBlank()
     */
    private $city;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Application\UserBundle\Entity\User", inversedBy="takens")
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Assert\DateTime()
     * @Assert\NotBlank()
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     * @Assert\DateTime()
     * @Assert\NotBlank()
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime")
     * @Assert\DateTime()
     * @Assert\NotBlank()
     */
    private $endDate;

    /**
     * @var int
     *
     * @ORM\Column(name="nbrPerson", type="integer", nullable=false)
     */
    private $nbrPerson;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(name="slug", type="string", length=64)
     * @Gedmo\Slug(fields={"title"}, style="lower", separator="-", updatable=false, unique=true)
     */
    private $slug;

    /**
     * @var bool
     * @ORM\Column(name="car", type="boolean" , nullable=false)
     */
    private $car;
    /**
     * @var int
     * @ORM\Column(name="car_place", type="integer")
     */
    private $carPlace;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\TakenParticipate", mappedBy="taken", orphanRemoval=true)
     */
    private $participates;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\TakenComment", mappedBy="taken")
     */
    private $comments;

    public function __construct()
    {
        $this->createdAt    = new \DateTime();
        $this->updatedAt    = new \DateTime();
        $this->participates = new ArrayCollection();
        $this->comments     = new ArrayCollection();
        $this->car          = false;
        $this->carPlace     = 0;
    }

    /**
     * @ORM\PrePersist()
     */
    public function preSlug()
    {
        $this->slug = $this->city->getName().'-'.$this->title;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Taken
     */
    public function setId(int $id): Taken
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Taken
     */
    public function setTitle(string $title): Taken
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Taken
     */
    public function setDescription(string $description): Taken
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     *
     * @return Taken
     */
    public function setCategory(Category $category): Taken
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     *
     * @return Taken
     */
    public function setCity(City $city): Taken
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Taken
     */
    public function setUser(User $user): Taken
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Taken
     */
    public function setCreatedAt(\DateTime $createdAt): Taken
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     *
     * @return Taken
     */
    public function setStartDate(\DateTime $startDate): Taken
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     *
     * @return Taken
     */
    public function setEndDate(\DateTime $endDate): Taken
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getNbrPerson()
    {
        return $this->nbrPerson;
    }

    /**
     * @param int $nbrPerson
     *
     * @return Taken
     */
    public function setNbrPerson(int $nbrPerson): Taken
    {
        $this->nbrPerson = $nbrPerson;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return Taken
     */
    public function setPrice(float $price): Taken
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCar()
    {
        return $this->car;
    }

    /**
     * @param bool $car
     *
     * @return Taken
     */
    public function setCar(bool $car): Taken
    {
        $this->car = $car;

        return $this;
    }

    /**
     * @return int
     */
    public function carPlace()
    {
        return $this->carPlace;
    }

    /**
     * @param int $nbr
     *
     * @return Taken
     */
    public function setCarPlace(int $nbr): Taken
    {
        $this->carPlace = $nbr;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getParticipates()
    {
        return $this->participates;
    }

    /**
     * @param TakenParticipate $takenParticipate
     *
     * @return Taken
     */
    public function addParticipate(TakenParticipate $takenParticipate):Taken
    {
        if (!$this->participates->contains($takenParticipate)) {
            $this->participates->add($takenParticipate);
        }

        return $this;
    }

    /**
     * @param TakenParticipate $takenParticipate
     */
    public function removeParticipate(TakenParticipate $takenParticipate)
    {
        $this->participates->removeElement($takenParticipate);
        $takenParticipate->setTaken(null);
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param TakenComment $takenComment
     *
     * @return Taken
     */
    public function addComment(TakenComment $takenComment):Taken
    {
        if (!$this->comments->contains($takenComment)) {
            $this->comments->add($takenComment);
        }

        return $this;
    }

    /**
     * @param TakenComment $takenComment
     */
    public function removeComment(TakenComment $takenComment)
    {
        $this->comments->removeElement($takenComment);
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getUploadDir()
    {
        return 'uploads/taken/';
    }

    /**
     * @return bool
     */
    public function isComplete()
    {
        return $this->nbrPerson <= $this->getNbrParticipate();
    }

    /**
     * @return int
     */
    public function getNbrParticipate()
    {
        $nbr = 0;
        if ($this->participates->isEmpty()) {
            return $nbr;
        }
        /** @var TakenParticipate $participate */
        foreach ($this->participates as $participate) {
            $nbr += $participate->getNbrPerson();
        }

        return $nbr;
    }

    /**
     * @return int
     */
    public function getNbrPossibleSubscriber()
    {
        $nbr = $this->nbrPerson - $this->getNbrParticipate();

        return max($nbr, 0);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isAlreadySubscribed(User $user)
    {
        if ($this->participates->isEmpty()) {
            return false;
        }
        /** @var TakenParticipate $participate */
        foreach ($this->participates as $participate) {
            if ($user === $participate->getUser()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return TakenParticipate|null
     */
    public function getParticipateByUser(User $user)
    {
        foreach ($this->participates as $participate) {
            if ($user === $participate->getUser()) {
                return $participate;
            }
        }

        return null;
    }
}
