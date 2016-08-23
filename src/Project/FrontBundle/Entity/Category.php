<?php

namespace Project\FrontBundle\Entity;

use Application\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\CategoryRepository")
 */
class Category
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
     * @ORM\Column(name="name", type="string", length=60, unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var ArrayCollection|User[]
     * @ORM\ManyToMany(targetEntity="Application\UserBundle\Entity\User", mappedBy="categorys", cascade={"persist"})
     */
    private $users;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\News", mappedBy="category")
     */
    private $news;

    /**
     * @var Context
     * @ORM\ManyToMany(targetEntity="Project\FrontBundle\Entity\Context", inversedBy="categorys")
     */
    private $contexts;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\Taken", mappedBy="category")
     */
    private $takens;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\Forum", mappedBy="category")
     */
    private $forums;


    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->users    = new ArrayCollection();
        $this->news     = new ArrayCollection();
        $this->contexts = new ArrayCollection();
        $this->takens   = new ArrayCollection();
        $this->forums   = new ArrayCollection();
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
     * @return Category
     */
    public function setName(string $name): Category
    {
        $this->name = $name;

        return $this;
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
     * @return Category
     */
    public function addUser(User $user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * @return ArrayCollection|News[]
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * @param News $news
     *
     * @return Category
     */
    public function addNews(News $news)
    {
        if (!$this->news->contains($news)) {
            $this->news->add($news);
            $news->setCategory($this);
        }

        return $this;
    }

    /**
     * @param News $news
     */
    public function removeNews(News $news)
    {
        $this->news->removeElement($news);
    }

    /**
     * @return ArrayCollection|Context[]
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * @param Context $context
     *
     * @return Category
     */
    public function addContext(Context $context)
    {
        if (!$this->contexts->contains($context)) {
            $this->contexts->add($context);
            $context->addCategory($this);
        }

        return $this;
    }

    /**
     * @param Context $context
     *
     * @return Category
     */
    public function removeContext(Context $context)
    {
        $this->contexts->removeElement($context);
        $context->removeCategory($this);

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
     * @return Category
     */
    public function addTaken(Taken $taken)
    {
        if (!$this->takens->contains($taken)) {
            $this->takens->add($taken);
            $taken->setCategory($this);
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
     * @return ArrayCollection|Forum[]
     */
    public function getForums()
    {
        return $this->forums;
    }

    /**
     * @param Forum $forum
     *
     * @return Category
     */
    public function addForum(Forum $forum)
    {
        if (!$this->forums->contains($forum)) {
            $this->forums->add($forum);
            $forum->setCategory($this);
        }

        return $this;
    }

    /**
     * @param Forum $forum
     */
    public function removeForum(Forum $forum)
    {
        $this->forums->removeElement($forum);
    }
}
