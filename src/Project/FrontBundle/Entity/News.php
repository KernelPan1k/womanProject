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
 * News
 *
 * @ORM\Table(name="news")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\NewsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class News implements UploadableInterface
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
     * @ORM\Column(name="title", type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="teaser", type="text", nullable=true)
     */
    private $teaser;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Application\UserBundle\Entity\User", inversedBy="news")
     */
    private $author;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Project\FrontBundle\Entity\Category", inversedBy="news")
     */
    private $category;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Assert\DateTime()
     */
    private $createdAt;

    /**
     * @var boolean
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @Gedmo\Slug(fields={"title"}, style="lower", separator="-", updatable=true, unique=true)
     * @ORM\Column(length=128, nullable=false)
     */
    private $slug;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\NewsComment", mappedBy="news")
     */
    private $comments;

    /**
     * News constructor.
     */
    public function __construct()
    {
        $this->comments  = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->enabled   = false;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return News
     */
    public function setTitle(string $title): News
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get teaser
     *
     * @return string
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * Set teaser
     *
     * @param string $teaser
     *
     * @return News
     */
    public function setTeaser(string $teaser): News
    {
        $this->teaser = $teaser;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return News
     */
    public function setContent(string $content): News
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get author
     *
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set author
     *
     * @param User $author
     *
     * @return News
     */
    public function setAuthor(User $author): News
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set category
     *
     * @param Category $category
     *
     * @return News
     */
    public function setCategory(Category $category): News
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return News
     */
    public function setCreatedAt(\DateTime $createdAt): News
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnabled(): Bool
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
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
        return 'uploads/news/';
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
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param NewsComment $newsComment
     *
     * @return News
     */
    public function addComment(NewsComment $newsComment):News
    {
        if (!$this->comments->contains($newsComment)) {
            $this->comments->add($newsComment);
        }

        return $this;
    }

    /**
     * @param NewsComment $newsComment
     */
    public function removeComment(NewsComment $newsComment)
    {
        $this->comments->removeElement($newsComment);
    }
}
