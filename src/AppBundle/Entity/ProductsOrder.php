<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProductsOrder
 *
 * @ORM\Table(name="products_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductsOrderRepository")
 */
class ProductsOrder
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
     * @Assert\NotNull(message="You must choose by what to order!")
     * @ORM\Column(name="orderBy", type="string", length=255, unique=true)
     */
    private $orderBy;

    /**
     * @var string
     *
     * @Assert\NotNull(message="You must choose a criteria!")
     *
     * @ORM\Column(name="criteria", type="string", length=255)
     */
    private $criteria;


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
     * Set orderBy
     *
     * @param string $orderBy
     *
     * @return ProductsOrder
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * Get orderBy
     *
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Set criteria
     *
     * @param string $criteria
     *
     * @return ProductsOrder
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * Get criteria
     *
     * @return string
     */
    public function getCriteria()
    {
        return $this->criteria;
    }
}

