<?php

namespace Knygmainys\UserBundle\Entity;

use Knygmainys\UserBundle\Entity\City;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
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
     *
     * @ORM\Column(name="first_name", type="string", length=35)
     *
     * @Assert\NotBlank(message="Įveskite savo vardą.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max="35",
     *     minMessage="Jūsų ?vestas vardas per trumpas.",
     *     maxMessage="Jūsų ?vestas vardas per ilgas.",
     *     groups={"Registration", "Profile"}
     * )
     * @Assert\Regex(
     *        pattern="/[a-zA-Z]/",
     *        message="Vardas gali būti sudarytas tik iš raidžių.",
     *        groups={"Registration", "Profile"}
     * )
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=35)
     *
     * @Assert\NotBlank(message="Įveskite savo vardą.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max="35",
     *     minMessage="Jūsų įvesta pavardė per trumpa.",
     *     maxMessage="Jūsų įvesta pavardė per ilga.",
     *     groups={"Registration", "Profile"}
     * )
     * @Assert\Regex(
     *        pattern="/[a-zA-Z]/",
     *        message="Pavardė gali būti sudaryta tik iš raidžių.",
     *        groups={"Registration", "Profile"}
     * )
     */
    protected $lastName;

    /**
     * @ORM\ManyToOne(targetEntity="Knygmainys\UserBundle\Entity\City")
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     *
     * @Assert\NotBlank(message="Įveskite savo adresą.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=10,
     *     max="255",
     *     minMessage="Jūsų įvestas adresas per trumpas.",
     *     maxMessage="Jūsų įvestas adresas per ilgas.",
     *     groups={"Registration", "Profile"}
     * )
     * @Assert\Regex(
     *        pattern="/[0-9a-zA-Z.,\- ]/",
     *        message="Adresas gali būti sudarytas tik iš raidžių, skaičių bei .,- simbolių.",
     *        groups={"Registration", "Profile"}
     * )
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="post_code", type="string", length=10)
     *
     * @Assert\NotBlank(message="Įveskite savo pašto kodą.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=5,
     *     max="10",
     *     minMessage="Jūsų įvestas pašto kodas per trumpas.",
     *     maxMessage="Jūsų įvestas pašto kodas per ilgas.",
     *     groups={"Registration", "Profile"}
     * )
     * @Assert\Regex(
     *        pattern="/[0-9a-zA-Z.,\- ]/",
     *        message="Pašto kodas gali būti sudarytas tik iš raidžių, skaičių bei - simbolio.",
     *        groups={"Registration", "Profile"}
     * )
     */
    protected $postCode;

    public function __construct()
    {
        parent::__construct();
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
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
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
     * @return User
     */
    public function setCity(City $city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @param string $postCode
     * @return User
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;
        return $this;
    }
}