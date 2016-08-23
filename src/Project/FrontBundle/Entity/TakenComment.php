<?php

namespace Project\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Response
 *
 * @ORM\Table(name="taken_comment")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\TakenCommentRepository")
 */
class TakenComment extends Comment
{
    /**
     * @var Taken
     *
     * @ORM\ManyToOne(targetEntity="Project\FrontBundle\Entity\Taken", inversedBy="comments")
     */
    private $taken;

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
     * @param Taken $taken
     *
     * @return TakenComment
     */
    public function setTaken(Taken $taken):TakenComment
    {
        $this->taken = $taken;
        $taken->addComment($this);

        return $this;
    }
}
