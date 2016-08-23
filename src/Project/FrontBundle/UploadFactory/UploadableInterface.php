<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 27.06.16
 * Time: 05:01
 */

namespace Project\FrontBundle\UploadFactory;

/**
 * Interface UploadableInterface
 * @package Project\FrontBundle\UploadFactory
 */
interface UploadableInterface
{
    public function getUpdatedAt();

    public function setUpdatedAt(\DateTime $updatedAt);

    public function getUploadDir();
}
