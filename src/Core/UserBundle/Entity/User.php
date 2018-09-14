<?php

namespace Core\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="Core\UserBundle\Repository\UserRepository")
 * @ORM\Table(name="ma_user")
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User extends BaseUser
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(name="lastName", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var datetime
     *
     * @ORM\Column(name="date_register", type="datetime")
     */
    private $date_register;

    public function __construct($type = 'ROLE_USER'){
        parent::__construct();
        $this->date_register = new \DateTime();
        $this->addRole($type);
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
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
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

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
     * Set address
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set date_register
     *
     * @param \DateTime $date_register
     * @return User
     */
    public function setDateRegister($date_register)
    {
        $this->date_register = $date_register;

        return $this;
    }

    /**
     * Get date_register
     *
     * @return \DateTime
     */
    public function getDateRegister()
    {
        return $this->date_register;
    }

    // EasyAdmin Virtual Properties : name
    public function getName()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

}

