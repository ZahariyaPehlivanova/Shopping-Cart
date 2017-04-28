<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Promotion
 *
 * @ORM\Table(name="promotion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PromotionRepository")
 */
class Promotion
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
     * @Assert\NotBlank()
     * @Assert\Length(min="3", minMessage="Promotion name must be at least 3 symbols long.")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="discount", type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min="1", max="99")
     * @Assert\Regex("/\d{1,2}/")
     */
    private $discount;

//    /**
//     * @var \DateTime
//     *
//     * @ORM\Column(name="duration", type="datetime")
//     * @Assert\GreaterThan("today")
//     * @Assert\NotBlank()
//     * @Assert\Date()
//     */
//    private $duration;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date")
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="date")
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $endDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $isDeleted;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Product", mappedBy="promotions")
     *
     * @var ArrayCollection
     */
    private $products;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Category", mappedBy="promotions")
     *
     * @var ArrayCollection
     */
    private $categories;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_category_promo", type="boolean")
     */
    private $isCategoryPromo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_all_products_promo", type="boolean")
     */
    private $isAllProductsPromo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_product_promo", type="boolean")
     */
    private $isProductPromo;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->isDeleted = false;
        $this->isProductPromo = false;
        $this->isCategoryPromo = false;
        $this->isAllProductsPromo = false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
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
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param int $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return bool
     */
    public function getIsDeleted()
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
     * @return ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param ArrayCollection $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * @return ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param ArrayCollection $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return bool
     */
    public function isIsCategoryPromo()
    {
        return $this->isCategoryPromo;
    }

    /**
     * @param bool $isCategoryPromo
     */
    public function setIsCategoryPromo($isCategoryPromo)
    {
        $this->isCategoryPromo = $isCategoryPromo;
    }

    /**
     * @return bool
     */
    public function isIsAllProductsPromo()
    {
        return $this->isAllProductsPromo;
    }

    /**
     * @param bool $isAllProductsPromo
     */
    public function setIsAllProductsPromo($isAllProductsPromo)
    {
        $this->isAllProductsPromo = $isAllProductsPromo;
    }

    /**
     * @return bool
     */
    public function isIsProductPromo()
    {
        return $this->isProductPromo;
    }

    /**
     * @param bool $isProductPromo
     */
    public function setIsProductPromo($isProductPromo)
    {
        $this->isProductPromo = $isProductPromo;
    }

    public function __toString()
    {
        return $this->getName();
    }
}

