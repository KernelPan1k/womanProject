<?php

namespace Project\FrontBundle\Entity;

use Application\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Project\FrontBundle\EntityTrait\UpdatedAtTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post
 *
 * @ORM\Table(name="forum_post")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\ForumPostRepository")
 */
class ForumPost
{

    use UpdatedAtTrait;

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
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Application\UserBundle\Entity\User", inversedBy="forumPosts")
     */
    private $user;

    /**
     * @var Forum
     *
     * @ORM\ManyToOne(targetEntity="Project\FrontBundle\Entity\Forum", inversedBy="posts")
     */
    private $forum;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Assert\DateTime()
     */
    private $createdAt;

    /**
     * @var ArrayCollection|PostResponse[]
     *
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\PostResponse", mappedBy="post")
     */
    private $responses;

    /**
     * @Gedmo\Slug(fields={"title"}, style="lower", separator="-", updatable=true, unique=true)
     * @ORM\Column(length=128, nullable=false)
     */
    private $slug;

    /**
     * ForumPost constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->responses = new ArrayCollection();
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
     * @return ForumPost
     */
    public function setTitle(string $title): ForumPost
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ForumPost
     */
    public function setDescription(string $description):ForumPost
    {
        $this->description = $description;

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
     * @return ForumPost
     */
    public function setUser(User $user): ForumPost
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get forum
     *
     * @return Forum
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * Set forum
     *
     * @param Forum $forum
     *
     * @return ForumPost
     */
    public function setForum(Forum $forum): ForumPost
    {
        $this->forum = $forum;

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
     * @return ForumPost
     */
    public function setCreatedAt(\DateTime $createdAt):ForumPost
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Add response
     *
     * @param PostResponse $response
     *
     * @return ForumPost
     */
    public function addResponse(PostResponse $response)
    {
        if (!$this->responses->contains($response)) {
            $response->setPost($this);
            $this->responses->add($response);
        }

        return $this;
    }

    /**
     * Remove response
     *
     * @param PostResponse $response
     */
    public function removeResponse(PostResponse $response)
    {
        $this->responses->removeElement($response);
    }

    /**
     * Get responses
     *
     * @return ArrayCollection|PostResponse[]
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param int $id
     *
     * @return null|PostResponse
     */
    public function getParentResponseById(int $id)
    {
        foreach ($this->responses as $response) {
            if (null === $response->getParent() && $response->getId() === $id) {
                return $response;
            }
        }

        return null;
    }
}
