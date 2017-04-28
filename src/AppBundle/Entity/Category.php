<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="The name of the category cannot be empty!")
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $isDeleted;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", mappedBy="category")
     *
     * @var Product[]|ArrayCollection $products
     */
    private $products;

    private $activeProducts;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Promotion", inversedBy="categories")
     * @ORM\JoinTable(name="category_promotions")
     *
     * @var ArrayCollection
     */
    private $promotions;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->isDeleted = false;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return mixed
     */
    public function getActiveProducts()
    {
        return $this->activeProducts;
    }

    /**
     * @param mixed $activeProducts
     */
    public function setActiveProducts($activeProducts)
    {
        $this->activeProducts = $activeProducts;
    }

    /**
     * @return ArrayCollection
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    public function addPromotion(Promotion $promotion)
    {
        $this->promotions[] = $promotion;
    }

    public function __toString()
    {
        return $this->getName();
    }
}

