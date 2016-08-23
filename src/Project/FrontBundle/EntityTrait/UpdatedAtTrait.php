<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 27.06.16
 * Time: 05:09
 */

namespace Project\FrontBundle\EntityTrait;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class UpdatedAtTrait
 * @package Project\FrontBundle\Entity
 */
trait UpdatedAtTrait
{

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updatedAt",type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
