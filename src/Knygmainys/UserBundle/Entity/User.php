<?php

namespace Knygmainys\UserBundle\Entity;

use Knygmainys\BooksBundle\Entity\HaveBook;
use Knygmainys\BooksBundle\Entity\WantBook;
use Knygmainys\UserBundle\Entity\City;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

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
     *     minMessage="Jūsų įvestas vardas per trumpas.",
     *     maxMessage="Jūsų įvestas vardas per ilgas.",
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
     *        pattern="/[0-9a-zA-Z\- ]/",
     *        message="Pašto kodas gali būti sudarytas tik iš raidžių, skaičių bei - simbolio.",
     *        groups={"Registration", "Profile"}
     * )
     */
    protected $postCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="current_points", type="integer", options={"default" : 3})
     */
    protected $currentPoints;

    /**
     * @var integer
     *
     * @ORM\Column(name="reserved_points", type="integer", options={"default" : 0})
     */
    protected $reservedPoints;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_points", type="integer", options={"default" : 3})
     */
    protected $totalPoints;

    /**
     * book owned association
     *
     * @ORM\OneToMany(targetEntity="Knygmainys\BooksBundle\Entity\HaveBook", mappedBy="user")
     */
    protected $ownedBooks;

    /**
     * book wanted association
     *
     * @ORM\OneToMany(targetEntity="Knygmainys\BooksBundle\Entity\WantBook", mappedBy="user")
     */
    protected $wantedBooks;

    /**
     * book contributed association
     *
     * @ORM\OneToMany(targetEntity="Knygmainys\BooksBundle\Entity\HaveBook", mappedBy="user")
     */
    protected $contributedBooks;

    /**
     * book received association
     *
     * @ORM\OneToMany(targetEntity="Knygmainys\BooksBundle\Entity\WantBook", mappedBy="user")
     */
    protected $receivedBooks;

    /**
     * @ORM\OneToMany(targetEntity="Knygmainys\UserBundle\Entity\NotificationUser", mappedBy="user")
     */
    protected $notifications;

    public function __construct()
    {
        parent::__construct();
        $this->notifications = new ArrayCollection();
        $this->ownedBooks = new ArrayCollection();
        $this->wantedBooks = new ArrayCollection();
        $this->contributedBooks = new ArrayCollection();
        $this->receivedBooks = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
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

    /**
     * @return integer
     */
    public function getCurrentPoints()
    {
        return $this->currentPoints;
    }

    /**
     * @param integer $currentPoints
     * @return User
     */
    public function setCurrentPoints($currentPoints)
    {
        $this->currentPoints = $currentPoints;
        return $this;
    }

    /**
     * @return integer
     */
    public function getReservedPoints()
    {
        return $this->reservedPoints;
    }

    /**
     * @return User
     */
    public function removeFromReserve($points)
    {
        $this->reservedPoints -= $points;

        return $this;
    }

    /**
     * @param integer $reservedPoints
     * @return User
     */
    public function setReservedPoints($reservedPoints)
    {
        $this->reservedPoints = $reservedPoints;
        return $this;
    }

    /**
     * @return integer
     */
    public function getTotalPoints()
    {
        return $this->totalPoints;
    }

    /**
     * @param integer $totalPoints
     * @return User
     */
    public function setTotalPoints($totalPoints)
    {
        $this->totalPoints = $totalPoints;
        return $this;
    }

    /**
     * @param $points
     * @return User
     */
    public function addEarnedPoints($points)
    {
        $this->currentPoints += $points;
        $this->totalPoints += $points;
        return $this;
    }

    /**
     * @param $points
     * @return User
     */
    public function addPoints($points)
    {
        $this->currentPoints += $points;
        return $this;
    }

    /**
     * @param $points
     * @return User
     */
    public function removePoints($points)
    {
        $this->currentPoints -= $points;
        return $this;
    }

    /**
     * @param $points
     * @return User
     */
    public function reservePoints($points)
    {
        $this->currentPoints -= $points;
        $this->reservedPoints += $points;
        return $this;
    }

    /**
     * @param $points
     * @return User
     */
    public function returnPoints($points)
    {
        $this->currentPoints += $points;
        $this->reservedPoints -= $points;
        return $this;
    }

    /**
     * Add owned book
     *
     * @param \Knygmainys\BooksBundle\Entity\HaveBook $book
     * @return User
     */
    public function addOwnedBooks($book)
    {
        $this->ownedBooks[] = $book;

        return $this;
    }

    /**
     * Remove owned book
     *
     * @param \Knygmainys\BooksBundle\Entity\HaveBook $book
     */
    public function removeOwnedBooks($book)
    {
        $this->ownedBooks->removeElement($book);
    }

    /**
     * Get owned books
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwnedBooks()
    {
        return $this->ownedBooks;
    }

    /**
     * Add wanted book
     *
     * @param \Knygmainys\BooksBundle\Entity\WantBook $book
     * @return User
     */
    public function addWantedBooks($book)
    {
        $this->wantedBooks[] = $book;

        return $this;
    }

    /**
     * Remove wanted book
     *
     * @param \Knygmainys\BooksBundle\Entity\WantBook $book
     */
    public function removeWantedBooks($book)
    {
        $this->wantedBooks->removeElement($book);
    }

    /**
     * Get wanted books
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWantedBooks()
    {
        return $this->wantedBooks;
    }



    /**
     * Add contributed book
     *
     * @param \Knygmainys\BooksBundle\Entity\HaveBook $book
     * @return User
     */
    public function addContributedBooks($book)
    {
        $this->contributedBooks[] = $book;

        return $this;
    }

    /**
     * Remove contributed book
     *
     * @param \Knygmainys\BooksBundle\Entity\HaveBook $book
     */
    public function removeContributedBooks($book)
    {
        $this->contributedBooks->removeElement($book);
    }

    /**
     * Get contributed books
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContributedBooks()
    {
        return $this->contributedBooks;
    }

    /**
     * Add received book
     *
     * @param \Knygmainys\BooksBundle\Entity\WantBook $book
     * @return User
     */
    public function addReceivedBooks($book)
    {
        $this->receivedBooks[] = $book;

        return $this;
    }

    /**
     * Remove received book
     *
     * @param \Knygmainys\BooksBundle\Entity\WantBook $book
     */
    public function removeReceivedBooks($book)
    {
        $this->receivedBooks->removeElement($book);
    }

    /**
     * Get received books
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReceivedBooks()
    {
        return $this->receivedBooks;
    }

    /**
     * Add notification to user
     *
     * @param \Knygmainys\UserBundle\Entity\NotificationUser $notification
     * @return User
     */
    public function addNotifications(NotificationUser $notification)
    {
        $this->notifications[] = $notification;

        return $this;
    }

    /**
     * Remove notification from user
     *
     * @param \Knygmainys\UserBundle\Entity\NotificationUser $notification
     */
    public function removeNotifications(NotificationUser $notification)
    {
        $this->notifications->removeElement($notification);
    }

    /**
     * Get notifications to user
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
}