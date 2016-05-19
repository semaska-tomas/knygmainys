<?php

namespace Knygmainys\BooksBundle\Entity;

use Knygmainys\BooksBundle\Entity\Author;
use Knygmainys\BooksBundle\Entity\Category;
use Knygmainys\BooksBundle\Entity\HaveBook;
use Knygmainys\BooksBundle\Entity\WantBook;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="books")
 * @ORM\Entity(repositoryClass="Knygmainys\BooksBundle\Repository\BookRepository")
 */
class Book
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
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Assert\NotBlank(message="Įveskite knygos pavadinimą.")
     * @Assert\Length(
     *     min="3",
     *     max="255",
     *     minMessage="Jūsų įvestas knygos pavadinimas per trumpas.",
     *     maxMessage="Jūsų įvestas knygos pavadinimas per ilgas."
     * )
     * @Assert\Regex(
     *        pattern="/[a-zA-Z0-9,.!?\-]/",
     *        message="Vardas gali būti sudarytas tik iš raidžių, skaičių ir ,.?! ir - simbolių."
     * )
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=1000)
     *
     * @Assert\NotBlank(message="Įveskite knygos pavadinimą.")
     * @Assert\Length(
     *     min="15",
     *     max="1000",
     *     minMessage="Jūsų įvestas knygos aprašymas per trumpas.",
     *     maxMessage="Jūsų įvestas knygos aprašymas per ilgas."
     * )
     * @Assert\Regex(
     *        pattern="/[a-zA-Z0-9,.!?\-]/",
     *        message="Aprašymas gali būti sudarytas tik iš raidžių, skaičių ir ,.?! ir - simbolių."
     * )
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Knygmainys\BooksBundle\Entity\Category")
     */
    protected $category;

    /**
     * @var integer
     *
     * @ORM\Column(name="created", type="date")
     */
    protected $created;

    /**
     * book release association
     *
     * @ORM\OneToMany(targetEntity="Knygmainys\BooksBundle\Entity\BookRelease", mappedBy="book")
     */
    protected $bookRelease;

    /**
     * book author association
     *
     * @ORM\OneToMany(targetEntity="Knygmainys\BooksBundle\Entity\BookAuthor", mappedBy="book")
     */
    protected $author;

    /**
     * wanted book association
     *
     * @ORM\OneToMany(targetEntity="Knygmainys\BooksBundle\Entity\WantBook", mappedBy="book")
     */
    protected $wantedBook;

    /**
     * owned book association
     *
     * @ORM\OneToMany(targetEntity="Knygmainys\BooksBundle\Entity\HaveBook", mappedBy="book")
     */
    protected $ownedBook;

    public function __construct()
    {
        $this->bookRelease = new ArrayCollection();
        $this->author = new ArrayCollection();
        $this->wantedBook = new ArrayCollection();
        $this->ownedBook = new ArrayCollection();
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
     * @return Book
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Book
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Book
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return Book
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get update time
     *
     * @return integer
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set update time
     *
     * @return HaveBook
     */
    public function setCreated()
    {
        $this->created = new \DateTime("now");

        return $this;
    }

    /**
     * Add bookReleases
     *
     * @param \Knygmainys\BooksBundle\Entity\BookRelease $bookRelease
     * @return Book
     */
    public function addBookRelease($bookRelease)
    {
        $this->bookRelease[] = $bookRelease;

        return $this;
    }

    /**
     * Remove bookRelease
     *
     * @param \Knygmainys\BooksBundle\Entity\BookRelease $bookRelease
     */
    public function removeBookRelease($bookRelease)
    {
        $this->bookRelease->removeElement($bookRelease);
    }

    /**
     * Get bookRelease
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookRelease()
    {
        return $this->bookRelease;
    }

    /**
     * Add bookAuthor
     *
     * @param \Knygmainys\BooksBundle\Entity\BookAuthor $author
     * @return Book
     */
    public function addAuthor($author)
    {
        $this->author[] = $author;

        return $this;
    }

    /**
     * Remove bookAuthor
     *
     * @param \Knygmainys\BooksBundle\Entity\BookAuthor $author
     */
    public function removeAuthor($author)
    {
        $this->author->removeElement($author);
    }

    /**
     * Get bookAuthor
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add owned book
     *
     * @param \Knygmainys\BooksBundle\Entity\HaveBook $book
     * @return Book
     */
    public function addOwnedBook($book)
    {
        $this->ownedBook[] = $book;

        return $this;
    }

    /**
     * Remove owned book
     *
     * @param \Knygmainys\BooksBundle\Entity\HaveBook $book
     */
    public function removeOwnedBook($book)
    {
        $this->ownedBook->removeElement($book);
    }

    /**
     * Get owned book
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwnedBook()
    {
        return $this->ownedBook;
    }

    /**
     * Add wanted book
     *
     * @param \Knygmainys\BooksBundle\Entity\WantBook $book
     * @return Book
     */
    public function addWantedBook($book)
    {
        $this->wantedBook[] = $book;

        return $this;
    }

    /**
     * Remove wanted book
     *
     * @param \Knygmainys\BooksBundle\Entity\WantBook $book
     */
    public function removeWantedBook($book)
    {
        $this->wantedBook->removeElement($book);
    }

    /**
     * Get wanted book
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWantedBook()
    {
        return $this->wantedBook;
    }
}
