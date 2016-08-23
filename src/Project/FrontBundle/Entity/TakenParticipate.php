<?php

namespace Project\FrontBundle\Entity;

use Application\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * TakenParticipate
 *
 * @ORM\Table(name="taken_participate")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\TakenParticipateRepository")
 */
class TakenParticipate
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
     * @var Taken
     *
     * @ORM\ManyToOne(targetEntity="Project\FrontBundle\Entity\Taken", inversedBy="participates")
     */
    private $taken;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Application\UserBundle\Entity\User", inversedBy="participates")
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="nbrPerson", type="integer")
     */
    private $nbrPerson;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get nbrPerson
     *
     * @return integer
     */
    public function getNbrPerson()
    {
        return $this->nbrPerson;
    }

    /**
     * Set nbrPerson
     *
     * @param integer $nbrPerson
     *
     * @return TakenParticipate
     */
    public function setNbrPerson(int $nbrPerson): TakenParticipate
    {
        $this->nbrPerson = $nbrPerson;

        return $this;
    }

    /**
     * Get taken
     *
     * @return Taken
     */
    public function getTaken()
    {
        return $this->taken;
    }

    /**
     * Set taken
     *
     * @param Taken $taken
     *
     * @return TakenParticipate
     */
    public function setTaken(Taken $taken = null): TakenParticipate
    {
        $this->taken = $taken;
        if ($taken instanceof Taken) {
            $taken->addParticipate($this);
        }

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return TakenParticipate
     */
    public function setUser(User $user):TakenParticipate
    {
        $this->user = $user;
        $user->addParticipate($this);

        return $this;
    }
}
