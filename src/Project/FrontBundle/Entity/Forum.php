<?php

namespace Project\FrontBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Project\FrontBundle\EntityTrait\UpdatedAtTrait;
use Project\FrontBundle\UploadFactory\UploadableInterface;
use Project\FrontBundle\UploadFactory\UploadableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Forum
 *
 * @ORM\Table(name="forum")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\ForumRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Forum implements UploadableInterface
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
     * @ORM\Column(name="name", type="string", length=100, unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Project\FrontBundle\Entity\Category", inversedBy="forums")
     */
    private $category;

    /**
     * @var ForumPost[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\ForumPost", mappedBy="forum")
     */
    private $posts;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @Assert\DateTime()
     */
    private $createdAt;

    /**
     * @Gedmo\Slug(fields={"name"}, style="lower", separator="-", updatable=true, unique=true)
     * @ORM\Column(length=128, nullable=false)
     */
    private $slug;

    /**
     * Forum constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->posts     = new ArrayCollection();
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
     * @return Forum
     */
    public function setName(string $name) :Forum
    {
        $this->name = $name;

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
     * @return Forum
     */
    public function setCreatedAt(\DateTime $createdAt): Forum
    {
        $this->createdAt = $createdAt;

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
     * @return Forum
     */
    public function setCategory(Category $category = null): Forum
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Add post
     *
     * @param ForumPost $post
     *
     * @return Forum
     */
    public function addPost(ForumPost $post):Forum
    {
        if (!$this->posts->contains($post)) {

            $post->setForum($this);
            $this->posts->add($post);
        }
    }

    /**
     * Remove post
     *
     * @param ForumPost $post
     */
    public function removePost(ForumPost $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get post
     *
     * @return ArrayCollection|ForumPost[]
     */
    public function getPosts()
    {
        return $this->posts;
    }

    public function getUploadDir()
    {
        return 'uploads/forum/';
    }
}
