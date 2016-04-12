<?php

namespace Knygmainys\BooksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="releases")
 * @ORM\HasLifecycleCallbacks
 */
class Release
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
     * @ORM\Column(name="isbn", type="string", length=100)
     *
     * @Assert\NotBlank(message="Įveskite knygos ISBN numerį.")
     * @Assert\Length(
     *     min="10",
     *     max="100",
     *     minMessage="Jūsų įvestas knygos pavadinimas per trumpas.",
     *     maxMessage="Jūsų įvestas knygos pavadinimas per ilgas."
     * )
     * @Assert\Regex(
     *        pattern="/[a-zA-Z0-9\-]/",
     *        message="ISBN numeris gali būti sudarytas tik iš raidžių, skaičių ir - simbolio."
     * )
     */
    protected $isbn;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=10)
     *
     * @Assert\NotBlank(message="Įveskite knygos išleidimo metus.")
     * @Assert\Length(
     *     min="4",
     *     max="10",
     *     minMessage="Įveskite knygos išleidimo metus.",
     *     maxMessage="Tinkamai įveskite knygos išleidimo metus."
     * )
     * @Assert\Regex(
     *        pattern="/[0-9\-]/",
     *        message="Metai gali būti sudaryti tik iš skaičių ir - simbolio."
     * )
     */
    protected $year;

    /**
     * @var string
     *
     * @ORM\Column(name="publishing_house", type="string", length=255)
     *
     * @Assert\NotBlank(message="Įveskite knygos leidyklos pavadinimą.")
     * @Assert\Length(
     *     min="3",
     *     max="255",
     *     minMessage="Jūsų įvestas knygos leidyklos pavadinimas per trumpas.",
     *     maxMessage="Jūsų įvestas knygos leidyklos pavadinimas per ilgas."
     * )
     * @Assert\Regex(
     *        pattern="/[a-zA-Z0-9\-]/",
     *        message="Knygos leidyklos pavadinimas gali būti sudarytas tik iš raidžių, skaičių ir - simbolio."
     * )
     */
    protected $publishingHouse;

    /**
     * Image file
     *
     * @var File
     *
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png"},
     *     maxSizeMessage = "Maksimalus paveikslėlio dydis 5mb.",
     *     mimeTypesMessage = "Tik jpeg, gig ir png failo formatai yra leidžiami."
     * )
     */
    protected $cover;

    /**
     * @var string
     *
     * @ORM\Column(name="cover_path", type="string", length=255, nullable=true)
     */
    private $coverPath;

    /**
     * team users association
     *
     * @ORM\OneToMany(targetEntity="Knygmainys\BooksBundle\Entity\BookRelease", mappedBy="release")
     */
    protected $bookRelease;

    public function __construct()
    {
        $this->bookRelease = new ArrayCollection();
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
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;
    }

    /**
     * @return string
     */
    public function getPublishingHouse()
    {
        return $this->publishingHouse;
    }

    /**
     * @param string $publishingHouse
     */
    public function setPublishingHouse($publishingHouse)
    {
        $this->publishingHouse = $publishingHouse;
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param string $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * Sets cover.
     *
     * @param UploadedFile $cover
     */
    public function setCover(UploadedFile $cover = null)
    {
        $this->cover = $cover;
    }

    /**
     * Get cover.
     *
     * @return UploadedFile
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Called before saving the entity
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->cover) {
            $filename = sha1(uniqid(mt_rand(), true));
            $this->coverPath = $filename.'.'.$this->cover->guessExtension();
        }
    }

    /**
     * Called before entity removal
     *
     * @ORM\PreRemove()
     */
    public function removeUpload()
    {
        if ($cover = $this->getAbsolutePath()) {
            unlink($cover);
        }
    }

    /**
     * Called after entity persistence
     *
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->cover) {
            return;
        }

        $this->cover->move(
            $this->getUploadRootDir(),
            $this->coverPath
        );

        $this->cover = null;
    }

    public function getAbsolutePath()
    {
        return null === $this->coverPath
            ? null
            : $this->getUploadRootDir().'/'.$this->coverPath;
    }

    public function getWebPath()
    {
        return null === $this->coverPath
            ? null
            : $this->getUploadDir().'/'.$this->coverPath;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/books';
    }

    /**
     * @return string
     */
    public function getCoverPath()
    {
        return $this->coverPath;
    }

    /**
     * @param string $coverPath
     */
    public function setCoverPath($coverPath)
    {
        $this->coverPath = $coverPath;
    }

    /**
     * Add bookReleases
     *
     * @param \Knygmainys\BooksBundle\Entity\BookRelease $bookRelease
     * @return Release
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
}