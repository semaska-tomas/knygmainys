<?php

namespace Knygmainys\BooksBundle\Entity;

use Knygmainys\BooksBundle\Entity\Book;
use Knygmainys\BooksBundle\Entity\Author;
use Doctrine\ORM\Mapping as ORM;

/**
 * BookAuthor
 *
 * @ORM\Table(name="books_authors")
 * @ORM\Entity
 */
class BookAuthor
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
     *
     * @ORM\ManyToOne(targetEntity="Knygmainys\BooksBundle\Entity\Book", inversedBy="author")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     *
     */
    private $book;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Knygmainys\BooksBundle\Entity\Author", inversedBy="book")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     *
     */
    private $author;

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
     * Set author
     *
     * @param \Knygmainys\BooksBundle\Entity\Author $author
     * @return BookRelease
     */
    public function setAuthor($author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \Knygmainys\BooksBundle\Entity\Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

}
