<?php

namespace Knygmainys\BooksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 */
class Category
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
     * @ORM\Column(name="name", type="string", length=35)
     *
     * @Assert\NotBlank(message="Pasirinkitę kategoriją.")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     *
     * @Assert\NotBlank(message="Įveskite kategorijos aprašymą.")
     * @Assert\Length(
     *     min="15",
     *     max="255",
     *     minMessage="Jūsų įvestas aprašymas per trumpas.",
     *     maxMessage="Jūsų įvestas aprašymas per ilgas."
     * )
     * @Assert\Regex(
     *        pattern="/[0-9a-zA-Z?!:.,\- ]/",
     *        message="Aprašymas gali būti sudarytas tik iš raidžių, skaičių bei leistinų simbolių."
     * )
     */
    protected $description;


    public function __construct()
    {

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
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}