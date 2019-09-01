<?php


namespace Filesharing\Entity;

use Carbon\Carbon;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\Entity as Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 *
 *
 * @Entity  @Table(name="users")
 *
 * @UniqueEntity(fields={"email"}, message="This email is already taken.")
 * @UniqueEntity(fields={"name"}, message="This name is already taken.")
 */
class User
{


    /**
     * @Assert\NotBlank(message="Error")
     * @Column(type="string")
     */
    protected $hash;

    /** @Id
     * @Column(type="integer") @GeneratedValue
     */
    protected $id;
    /**
     * @Assert\NotBlank(message="Name cannot be not blank")
     * @Assert\Length(max = 20, maxMessage="Name too short")
     * @Column(type="string", length =20, unique=true)
     */
    public $name;
    /**
     *
     *
     * @Assert\Email
     * @Assert\NotBlank(message="Email cannot be not blank")
     * @Column(type="string", unique=true)
     */
    protected $email;
    /**
     *
     * @Assert\NotBlank(message="Password cannot be not blank")
     * @Assert\Length(
     *     min=5,
     *     minMessage="Your password is too short",
     * )
     * @Column(type="string")
     */
    protected $password;

    /**
     * @Assert\NotBlank(message="Error")
     *    * @Column(type="datetime")
     */
    protected $created_at;
    /**
     *
     * @Column(type="string")
     */
    protected $avatarPath;

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
    public function getAvatarPath()
    {
        return $this->avatarPath;
    }

    /**
     * @param mixed $avatarPath
     */
    public function setAvatarPath($avatarPath)
    {
        $this->avatarPath = $avatarPath;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
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
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }


}