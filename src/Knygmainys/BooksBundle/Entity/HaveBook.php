<?php

namespace Knygmainys\BooksBundle\Entity;

use Knygmainys\BooksBundle\Entity\Book;
use Knygmainys\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * BookAuthor
 *
 * @ORM\Table(name="have_books")
 * @ORM\Entity
 */
class HaveBook
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     *
     */
    protected $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="created", type="date")
     */
    protected $updated;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Knygmainys\BooksBundle\Entity\Book", inversedBy="ownedBook")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     *
     */
    protected $book;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Knygmainys\BooksBundle\Entity\Release", inversedBy="ownedReleases")
     * @ORM\JoinColumn(name="release_id", referencedColumnName="id")
     *
     */
    protected $release;


    /**
     *
     * @ORM\ManyToOne(targetEntity="Knygmainys\UserBundle\Entity\User", inversedBy="ownedBooks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     */
    protected $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Knygmainys\UserBundle\Entity\User", inversedBy="receivedBooks")
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id", nullable=true)
     *
     */
    protected $receiver;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return HaveBook
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get update time
     *
     * @return integer
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set update time
     *
     * @return WantBook
     */
    public function setUpdated()
    {
        $this->updated = new \DateTime("now");

        return $this;
    }

    /**
     * Set book
     *
     * @param \Knygmainys\BooksBundle\Entity\Book $book
     * @return HaveBook
     */
    public function setBook($book = null)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return \Knygmainys\BooksBundle\Entity\Book
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * Set user
     *
     * @param \Knygmainys\UserBundle\Entity\User $user
     * @return HaveBook
     */
    public function setUser($user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Knygmainys\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set release
     *
     * @param \Knygmainys\BooksBundle\Entity\Release $release
     * @return HaveBook
     */
    public function setRelease($release = null)
    {
        $this->release = $release;

        return $this;
    }

    /**
     * Get release
     *
     * @return \Knygmainys\BooksBundle\Entity\Release
     */
    public function getRelease()
    {
        return $this->release;
    }

    /**
     * Set receiver
     *
     * @param \Knygmainys\UserBundle\Entity\User $user
     * @return HaveBook
     */
    public function setReceiver($user = null)
    {
        $this->receiver = $user;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return \Knygmainys\UserBundle\Entity\User
     */
    public function getReceiver()
    {
        return $this->receiver;
    }
}
