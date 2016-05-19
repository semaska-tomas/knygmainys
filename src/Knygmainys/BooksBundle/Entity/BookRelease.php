<?php

namespace Knygmainys\BooksBundle\Entity;

use Knygmainys\BooksBundle\Entity\Book;
use Knygmainys\BooksBundle\Entity\Release;
use Doctrine\ORM\Mapping as ORM;

/**
 * BookRelease
 *
 * @ORM\Table(name="books_releases")
 * @ORM\Entity
 */
class BookRelease
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Knygmainys\BooksBundle\Entity\Book", inversedBy="bookRelease")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     *
     */
    protected $book;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Knygmainys\BooksBundle\Entity\Release", inversedBy="bookRelease")
     * @ORM\JoinColumn(name="release_id", referencedColumnName="id")
     *
     */
    protected $release;

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
     * Set book
     *
     * @param \Knygmainys\BooksBundle\Entity\Book $book
     * @return BookRelease
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
     * Set release
     *
     * @param \Knygmainys\BooksBundle\Entity\Release $release
     * @return BookRelease
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
