<?php


namespace Filesharing\Entity;

use Carbon\Carbon;
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
 * @Entity  @Table(name="comments")
 */
class Comment
{

    /**
     * @Id
     * @Column(type="integer") @GeneratedValue
     */
    protected $id;
    /**
     * @Assert\NotBlank
     * @Column(type="string")
     */
    protected $text;
    /**
     *
     *
     * @Assert\NotBlank
     * @Column(type="datetime")
     */
    protected $created_at;
    /**
     *
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    /**
     * @ManyToOne(targetEntity="File", inversedBy="comments")
     * @JoinColumn(name="file_id",referencedColumnName="id", nullable=true)
     */
    protected $file;
    /**
     * One Category has Many Categories.
     * @OneToMany(targetEntity="Comment", mappedBy="parent")
     * @OrderBy({"created_at" = "DESC"})
     */
    private $children;
    /**
     * Many Categories have One Category.
     * @ManyToOne(targetEntity="Comment", inversedBy="children")
     * @JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children ?? false;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return Carbon::createFromDate($this->created_at->format('m/d/Y H:i:s'));
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($createdAt)
    {

        $this->created_at = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user_id
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file_id
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

}