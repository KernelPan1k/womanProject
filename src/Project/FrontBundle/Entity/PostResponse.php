<?php

namespace Project\FrontBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Response
 *
 * @ORM\Table(name="post_response")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\PostResponseRepository")
 */
class PostResponse extends Comment
{
    /**
     * @var ForumPost
     *
     * @ORM\ManyToOne(targetEntity="Project\FrontBundle\Entity\ForumPost", inversedBy="responses")
     * @Assert\NotNull()
     */
    private $post;

    /**
     * @var PostResponse
     * @ORM\ManyToOne(targetEntity="Project\FrontBundle\Entity\PostResponse", inversedBy="childs")
     */
    private $parent;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\PostResponse", mappedBy="parent", cascade={"persist"})
     */
    private $childs;

    /**
     * @var int
     * @ORM\Column(name="liked", nullable=false)
     */
    private $like;

    /**
     * PostResponse constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->childs = new ArrayCollection();
        $this->like   = 0;
    }

    /**
     * Get post
     *
     * @return ForumPost
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set post
     *
     * @param ForumPost $post
     *
     * @return PostResponse
     */
    public function setPost(ForumPost $post):PostResponse
    {
        $this->post = $post;
        $post->setUpdatedAt(new \DateTime());

        return $this;
    }

    /**
     * @return PostResponse
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param PostResponse|null $parent
     *
     * @return PostResponse
     */
    public function setParent(PostResponse $parent = null):PostResponse
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return int
     */
    public function getLike(): int
    {
        return $this->like;
    }

    /**
     * @param int $like
     *
     * @return PostResponse
     */
    public function setLike(int $like): PostResponse
    {
        $this->like = $like;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * @param PostResponse $postResponse
     *
     * @return PostResponse
     */
    public function addChild(PostResponse $postResponse): PostResponse
    {
        if (!$this->childs->contains($postResponse)) {
            $postResponse->setParent($this);
            $this->childs->add($postResponse);
        }

        return $this;
    }

    /**
     * @param PostResponse $postResponse
     */
    public function removeChild(PostResponse $postResponse)
    {
        $this->childs->removeElement($postResponse);
    }
}
