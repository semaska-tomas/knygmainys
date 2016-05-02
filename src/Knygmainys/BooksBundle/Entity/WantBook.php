<?php

namespace Knygmainys\BooksBundle\Entity;

use Knygmainys\BooksBundle\Entity\Book;
use Knygmainys\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * BookAuthor
 *
 * @ORM\Table(name="want_books")
 * @ORM\Entity
 */
class WantBook
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
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="points", type="integer")
     */
    private $points;

    /**
     * @var integer
     *
     * @ORM\Column(name="datetime", type="date")
     */
    private $updated;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Knygmainys\BooksBundle\Entity\Book", inversedBy="wantedBook")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     *
     */
    private $book;

    /**
    *
    * @ORM\ManyToOne(targetEntity="Knygmainys\BooksBundle\Entity\Release", inversedBy="wantedReleases")
    * @ORM\JoinColumn(name="release_id", referencedColumnName="id")
    *
    */
    private $release;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Knygmainys\UserBundle\Entity\User", inversedBy="wantedBooks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     */
    private $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Knygmainys\UserBundle\Entity\User", inversedBy="contributedBooks")
     * @ORM\JoinColumn(name="contributor_id", referencedColumnName="id", nullable=true)
     *
     */
    private $contributor;

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
     * @return WantBook
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set points
     *
     * @param $points
     * @return WantBook
     */
    public function setPoints($points)
    {
        $this->points = $points;

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
     * @return WantBook
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
     * @return WantBook
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
     * @return WantBook
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

}
