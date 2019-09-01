<?php
// app/Entity/File.php
namespace Filesharing\Entity;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\Entity as Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OrderBy;


/**
 *
 *
 * @Entity  @Table(name="files")
 *
 *
 */
class File
{

    /** @Id
     *
     *
     *
     * @Column(type="integer") @GeneratedValue
     */
    protected $id;
    /**
     *
     * @Column(type="integer")
     **/
    protected $size;


    /**
     * @Assert\NotBlank(message="Filename cannot be not blank")
     * @Column(type="string")
     */

    protected $name;
    /**
     *
     *
     *
     * @Column(type="string")
     **/
    protected $type;

    /**
     *
     * @Assert\Length(
     *     max = 170,
     *     maxMessage = "Description name cannot be longer than {{ limit }} characters"
     * )
     * @Column(type="string", length=170,nullable=true)
     */

    protected $description = '';

    /**
     *
     * @Column(type="datetime")
     */
    protected $created_at = '';
    /**  @Column(type="string", nullable=true) ) */
    protected $updated_at = '';
    /**
     *
     *
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $user = null;
    /**
     *
     * @Column(type="string")
     */
    protected $public_path;


    /**
     *
     * @Column(type="integer", nullable=true)
     */
    protected $likes;
    /**
     *
     * @Column(type="integer", nullable=true)
     */
    protected $dislikes;
    /**
     *
     * @Column(type="string")
     */
    protected $imagePath;

    /**
     *
     * @OneToMany(targetEntity="Comment",mappedBy="file")
     * @OrderBy({"created_at" = "DESC"})
     *
     *
     */
    protected $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }


    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments->add($comments);
    }

    /**
     * @return mixed
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param mixed $likes
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;
    }

    /**
     * @return mixed
     */
    public function getDislikes()
    {
        return $this->dislikes;
    }

    /**
     * @param mixed $dislikes
     */
    public function setDislikes($dislikes)
    {
        $this->dislikes = $dislikes;
    }

    /**
     * @return mixed
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * @param mixed $imagePath
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;
    }


    public function getDescription()
    {
        return $this->description;
    }


    public function setDescription($description)
    {
        $this->description = $description;
    }


    public function getCreatedAt()
    {
        return Carbon::createFromDate($this->created_at->format('m/d/Y H:i:s'));
    }


    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }


    public function getUpdatedAt()
    {
        return $this->updated_at;
    }


    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }


    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }


    public function getPublicPath()
    {
        return $this->public_path;
    }


    public function setPublicPath($public_path)
    {
        $this->public_path = $public_path;
    }


    public function getId()
    {
        return $this->id;
    }


    public function setId($id)
    {
        $this->id = $id;
    }


    public function getSize()
    {
        return $this->size;
    }


    public function setSize($size)
    {
        $this->size = $size;
    }


    public function getName()
    {
        return $this->name;
    }


    public function setName($name)
    {
        $this->name = $name;
    }


    public function getType()
    {
        return $this->type;
    }


    public function setType($type)
    {
        $this->type = $type;
    }
}