<?php

namespace Project\FrontBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Context
 *
 * @ORM\Table(name="context")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\ContextRepository")
 */
class Context
{

    const CONTEXT_USER = 'USER';
    const CONTEXT_NEWS = 'NEWS';
    const CONTEXT_TAKEN = 'TAKEN';
    const CONTEXT_FORUM = 'FORUM';

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
     * @ORM\Column(name="context", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $context;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Project\FrontBundle\Entity\Category", mappedBy="contexts")
     */
    private $categorys;

    /**
     * Context constructor.
     */
    public function __construct()
    {
        $this->categorys = new ArrayCollection();
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
     * Get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set context
     *
     * @param string $context
     *
     * @return Context
     */
    public function setContext(string $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCategorys()
    {
        return $this->categorys;
    }

    /**
     * @param Category $category
     *
     * @return $this
     */
    public function addCategory(Category $category)
    {
        if (!$this->categorys->contains($category)) {
            $this->categorys->add($category);
        }

        return $this;
    }

    /**
     * @param Category $category
     */
    public function removeCategory(Category $category)
    {
        if ($this->categorys->contains($category)) {
            $this->categorys->removeElement($category);
        }
    }
}
