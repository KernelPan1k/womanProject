<?php

namespace Application\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Project\FrontBundle\Entity\Category;
use Project\FrontBundle\Entity\City;
use Project\FrontBundle\Entity\ForumPost;
use Project\FrontBundle\Entity\News;
use Project\FrontBundle\Entity\Taken;
use Project\FrontBundle\Entity\TakenParticipate;
use Project\FrontBundle\EntityTrait\UpdatedAtTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser implements EncoderAwareInterface
{
    use UpdatedAtTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=40)
     * @Assert\NotBlank(groups={"mandatory"}, message="Le prénom est obligatoire")
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=40)
     * @Assert\NotBlank(groups={"mandatory"}, message="Le nom est obligatoire")
     */
    protected $lastName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateOfBirth", type="datetime")
     * @Assert\DateTime()
     * @Assert\NotBlank(groups={"mandatory"}, message="La date de naisssance obligatoire")
     */
    protected $dateOfBirth;

    /**
     * @var City
     * @ORM\ManyToOne(targetEntity="Project\FrontBundle\Entity\City", inversedBy="users")
     * @Assert\NotBlank(groups={"mandatory"}, message="La ville est obligatoire")
     */
    private $city;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Assert\NotBlank(groups={"mandatory"}, message="Le texte de description est obligatoire")
     */
    private $description;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Project\FrontBundle\Entity\Category", inversedBy="users", cascade={"persist"} )
     * @ORM\OrderBy({"name": "ASC"})
     * @Assert\Count(groups={"mandatory"}, minMessage="Vous devez renseigner au minimum un centre d'intérêt", min="1")
     */
    private $categorys;

    /**
     * @Gedmo\Slug(fields={"username"}, style="lower", separator="-", updatable=false, unique=true)
     * @ORM\Column(length=128, unique=true, nullable=false)
     */
    private $slug;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @Assert\DateTime()
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(name="img", type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * @var string
     * @ORM\Column(name="big_img", type="string", length=255, nullable=true)
     */
    private $bigImg;

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
     * upload file
     * @var UploadedFile
     * @File(
     *     maxSizeMessage = "L'image ne doit pas dépasser 500k.",
     *     maxSize = "512k",
     *     mimeTypes = {"image/jpg", "image/jpeg", "image/png"},
     *     mimeTypesMessage = "Les images doivent être au format JPG, PNG."
     * )
     */
    private $bigFile;

    private $temp = null;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\News", mappedBy="author")
     */
    private $news;

    /**
     * @var ArrayCollection|Taken[]
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\Taken", mappedBy="user")
     */
    private $takens;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\ForumPost", mappedBy="user")
     */
    private $forumPosts;

    /**
     * @var ORM\OneToMany
     * @ORM\OneToMany(targetEntity="Project\FrontBundle\Entity\TakenParticipate", mappedBy="user")
     */
    private $participates;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->categorys    = new ArrayCollection();
        $this->news         = new ArrayCollection();
        $this->takens       = new ArrayCollection();
        $this->forumPosts   = new ArrayCollection();
        $this->participates = new ArrayCollection();
        $this->createdAt    = new \DateTime();
        $this->updatedAt    = new \DateTime();
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get old
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set old
     *
     * @param \DateTime $dateOfBirth
     *
     * @return User
     */
    public function setDateOfBirth(\DateTime $dateOfBirth): User
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     *
     * @return User
     */
    public function setCity(City $city): User
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return ArrayCollection|Category[]
     */
    public function getCategorys()
    {
        return $this->categorys;
    }

    /**
     * @param Category $category
     *
     * @return User
     */
    public function addCategory(Category $category): User
    {

        if (!$this->categorys->contains($category)) {

            $category->addUser($this);
            $this->categorys->add($category);
        }

        return $this;
    }

    /**
     * @param Category $category
     */
    public function removeCategory(Category $category)
    {

        $this->categorys->removeElement($category);
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return User
     */
    public function setDescription(string $description = null): User
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * @param News $news
     *
     * @return User
     */
    public function addNews(News $news)
    {
        if (!$this->news->contains($news)) {
            $this->news->add($news);
            $news->setAuthor($this);
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
     * @return User
     */
    public function setImg(string $img = null): User
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get bigImg
     *
     * @return string
     */
    public function getBigImg()
    {
        return $this->bigImg;
    }

    /**
     * Set bigImg
     *
     * @param string $bigImg
     *
     * @return User
     */
    public function setBigImg(string $bigImg = null): User
    {
        $this->bigImg = $bigImg;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**news
     *
     * @param UploadedFile $file
     *
     * @return User
     */
    public function setFile(UploadedFile $file)
    {
        $this->file      = $file;
        $this->updatedAt = new \DateTime(); // force event update

        if (null !== $this->img) { // update
            $this->temp = $this->id; // folder
        }

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getBigFile()
    {
        return $this->bigFile;
    }

    /**
     * @param UploadedFile $bigFile
     *
     * @return User
     */
    public function setBigFile(UploadedFile $bigFile)
    {
        $this->bigFile   = $bigFile;
        $this->updatedAt = new \DateTime(); // force event update

        if (null !== $this->bigImg) { // update
            $this->temp = $this->id; // folder
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
        if (null !== $this->bigImg) {
            $this->temp = $this->id;
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {

        if (null === $this->temp) {
            return;
        }

        $folders = ['avatar', 'background'];

        foreach ($folders as $folder) {
            $dir = $this->getUploadDir().$this->temp.'/'.$folder;
            if (is_dir($dir)) {
                $files = glob($dir.'/*');
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
                rmdir($dir);
            }
        }

        rmdir($this->getUploadDir().$this->temp);
    }

    public function getUploadDir()
    {
        return 'uploads/user/';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null === $this->file && null === $this->bigFile) {
            return;
        }
        if (null !== $this->file && null !== $this->file->guessExtension()) {
            $this->img = $this->rename($this->file->getClientOriginalName(), $this->file->guessExtension());
        }
        if (null !== $this->bigFile && null !== $this->bigFile->guessExtension()) {
            $this->bigImg = $this->rename($this->bigFile->getClientOriginalName(), $this->bigFile->guessExtension());
        }
    }

    private function rename(string $string, string $ext)
    {
        $slug = md5(uniqid().$string.uniqid()).'.'.$ext;

        return $slug;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file && null === $this->bigFile) {
            return;
        }

        if (null !== $this->temp) {

            $oldDir = $this->getUploadDir().$this->temp.'/';

            if (null !== $this->file && is_dir($oldDir.'avatar')) {
                $files = glob($oldDir.'avatar/*');
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
            if (null !== $this->bigFile && is_dir($oldDir.'background')) {
                $files = glob($oldDir.'background/*');
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
        }

        if (null !== $this->file) {
            $this->file->move($this->getUploadDir().$this->id.'/avatar/', $this->img);
        }

        if (null !== $this->bigFile) {
            $this->bigFile->move($this->getUploadDir().$this->id.'/background/', $this->bigImg);
        }
    }

    public function getAvatarPath()
    {
        return $this->getWebPath().'avatar/'.$this->img;
    }

    public function getWebPath()
    {
        return '/'.$this->getUploadDir().$this->getId().'/';
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

    public function getBackgroundPath()
    {
        return $this->getWebPath().'background/'.$this->bigImg;
    }

    /**
     * Gets the name of the encoder used to encode the password.
     *
     * If the method returns null, the standard way to retrieve the encoder
     * will be used instead.
     *
     * @return string
     */
    public function getEncoderName()
    {
        if ($this->isSuperAdmin()) {
            return 'harsh';
        }

        return 'default_hash';
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function categorysToString()
    {
        $str = '';
        foreach ($this->categorys as $cat) {
            $str .= $cat->getName().', ';
        }

        return substr($str, 0, -2);
    }


    public function isProfilComplete()
    {
        return
            null !== $this->lastLogin
            && false === $this->locked
            && null !== $this->city
            && null !== $this->description
            && null !== $this->categorys
            && !$this->categorys->isEmpty();
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
     * @return User
     */
    public function addTakens(Taken $taken)
    {
        if (!$this->takens->contains($taken)) {
            $this->takens->add($taken);
            $taken->setUser($this);
        }

        return $this;
    }

    /**
     * @param Taken $taken
     */
    public function removeTakens(Taken $taken)
    {
        $this->takens->removeElement($taken);
    }

    /**
     * @return ArrayCollection|ForumPost[]
     */
    public function getForumPosts()
    {
        return $this->forumPosts;
    }

    /**
     * @param ForumPost $forumPost
     *
     * @return User
     */
    public function addForumPost(ForumPost $forumPost)
    {
        if (!$this->forumPosts->contains($forumPost)) {
            $this->forumPosts->add($forumPost);
            $forumPost->setUser($this);
        }

        return $this;
    }

    /**
     * @param ForumPost $forumPost
     */
    public function removeForumPost(ForumPost $forumPost)
    {
        $this->forumPosts->removeElement($forumPost);
    }

    /**
     * @return ArrayCollection
     */
    public function getParticipates()
    {
        return $this->participates;
    }


    public function addParticipate(TakenParticipate $takenParticipate):User
    {
        if (!$this->participates->contains($takenParticipate)) {
            $this->participates->add($takenParticipate);
        }

        return $this;
    }

    /**
     * @param TakenParticipate $takenParticipate
     */
    public function removeParticipate(TakenParticipate $takenParticipate)
    {
        $this->participates->removeElement($takenParticipate);
    }

}
