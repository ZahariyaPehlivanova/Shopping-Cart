<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="products")
     * @ORM\JoinColumn(name="seller_id", referencedColumnName="id")
     */
    private $seller;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255)
     */
    private $image;

    /**
     * @Assert\Image(mimeTypes={"image/png", "image/jpeg"}, maxSize="5M")
     */
    private $image_form;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=11, scale=2)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="promotionPrice", type="decimal", precision=11, scale=2)
     */
    private $promotionPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdOn", type="datetimetz")
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedOn", type="datetimetz")
     */
    private $updatedOn;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="products")
     *
     * @var ArrayCollection
     */
    private $buyers;

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="integer")
     */
    private $rating = 0;

    /**
     * @var Category $category
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category", inversedBy="products")
     * @Assert\NotBlank()
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Promotion", inversedBy="products")
     * @ORM\JoinTable(name="product_promotions")
     *
     * @var ArrayCollection
     */
    private $promotions;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Review", mappedBy="product")
     *
     * @var Review[]|ArrayCollection $reviews
    */
    private $reviews;

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
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set seller
     *
     * @param User $seller
     *
     * @return Product
     */
    public function setSeller(User $seller)
    {
        $this->seller = $seller;

        return $this;
    }

    /**
     * Get seller
     *
     * @return User
     */
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Product
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Product
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return Product
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return Product
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    /**
     * Get updatedOn
     *
     * @return \DateTime
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    /**
     * Set buyers
     *
     * @param array $buyers
     *
     * @return Product
     */
    public function setBuyers($buyers)
    {
        $this->buyers = $buyers;

        return $this;
    }

    /**
     * Get buyers
     *
     * @return ArrayCollection
     */
    public function getBuyers()
    {
        return $this->buyers;
    }

    /**
     * @return mixed
     */
    public function getImageForm()
    {
        return $this->image_form;
    }

    /**
     * @param mixed $image_form
     */
    public function setImageForm($image_form)
    {
        $this->image_form = $image_form;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return ArrayCollection
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * @param ArrayCollection $promotions
     */
    public function setPromotions($promotions)
    {
        $this->promotions = $promotions;
    }

    public function addPromotion(Promotion $promotion)
    {
        $this->promotions[] = $promotion;
    }

    /**
     * @return string
     */
    public function getPromotionPrice()
    {
        return $this->promotionPrice;
    }

    /**
     * @param string $promotionPrice
     */
    public function setPromotionPrice($promotionPrice)
    {
        $this->promotionPrice = $promotionPrice;
    }

    /**
     * @return mixed
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param mixed $reviews
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;
    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }



    public function __toString()
    {
        return $this->getName();
    }
}

