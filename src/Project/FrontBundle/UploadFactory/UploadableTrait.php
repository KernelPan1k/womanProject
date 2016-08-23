<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 27.06.16
 * Time: 05:03
 */

namespace Project\FrontBundle\UploadFactory;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

/**
 * Class UplodableTrait
 * @package Project\FrontBundle\UploadFactory
 * @ORM\HasLifecycleCallbacks()
 */
trait UploadableTrait
{
    /**
     * alt attribute
     * @var string
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * image name with extension
     * @var string
     * @ORM\Column(name="img", type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * upload file
     * @var UploadedFile
     * @File(
     *     maxSizeMessage = "L'image ne doit pas dépasser 500k.",
     *     maxSize = "512k",
     *     mimeTypes = {"image/jpg", "image/jpeg", "image/png"},
     *     mimeTypesMessage = "Les images doivent être au format JPG, PNG."
     * )
     */
    private $file;

    /**
     * @var int
     * stock id when entity has removed for delete folder
     */
    private $temp = null;

    /**
     * @return integer|null
     */
    public function getTemp()
    {
        return $this->temp;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set name
     *
     * @param string $alt
     */
    public function setAlt(string $alt = null)
    {
        $this->alt = $alt;
    }

    /**
     * Get img
     *
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set img
     *
     * @param string $img
     *
     */
    public function setImg(string $img)
    {
        $this->img = $img;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     *
     * @return $this
     */
    public function setFile(UploadedFile $file)
    {
        $this->file      = $file;
        $this->updatedAt = new \DateTime();
        /** force event update **/

        if (null !== $this->img) {
            /** update */
            $this->temp = $this->id;
        }

        return $this;
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        if (null !== $this->img) {
            $this->temp = $this->id;
        }

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null === $this->file) {
            return null;
        }

        $ext = $this->file->guessExtension();

        if (null === $ext) {
            $this->file = null;

            return null;
        }

        $this->img = $this->rename($this->file->getClientOriginalName(), $ext);

        return $this;
    }

    /**
     * @param string $string
     *
     * @param string $ext
     *
     * @return string
     */
    private function rename(string $string, string $ext)
    {
        $name = md5(uniqid().$string.uniqid()).'.'.$ext;

        return $name;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (null === $this->temp) {
            return null;
        }
        $dir = $this->getTempDir();
        if (is_dir($dir)) {
            $files = glob($dir.'/*');
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            rmdir($dir);
        }

        return $this;
    }

    /**
     * @return string
     */
    private function getTempDir()
    {
        return $this->getUploadDir().$this->temp;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return null;
        }
        if (null !== $this->temp) {
            $oldDir = $this->getTempDir();
            if (is_dir($oldDir)) {
                $files = glob($oldDir.'/*');
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
        }
        $this->file->move($this->getIdDir(), $this->img);

        return $this;
    }

    /**
     * @return string
     */
    private function getIdDir()
    {
        return $this->getUploadDir().$this->id;
    }

    /**
     * @return string
     */
    public function getImgPath()
    {
        return $this->getWebPath().$this->img;
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
        return '/'.$this->getUploadDir().$this->getId().'/';
    }
}
