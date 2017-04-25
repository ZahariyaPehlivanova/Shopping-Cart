<?php

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManager;

class PriceCalculator
{
    /**
     * @var EntityManager
    */
    protected $em;

    protected $promotion;

    /**
     * PriceCalculator constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Product $product
    */
    public function calcPrice(Product $product){

        $promotion = $this->em->getRepository('AppBundle:Promotion')->fetchBiggestPromotion($product);
        if($promotion){
            $this->promotion = $promotion;
            $discount = $this->promotion->getDiscount();

            $price = $product->getPrice();
            return $price - ($price * ($discount / 100));
        }
        return $product->getPrice();
    }
}