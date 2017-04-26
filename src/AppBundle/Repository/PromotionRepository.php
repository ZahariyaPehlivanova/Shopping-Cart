<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Product;
use AppBundle\Entity\Promotion;
use Symfony\Component\Validator\Constraints\DateTime;

class PromotionRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllActivePromotions()
    {
        return $this->createQueryBuilder('promotion')
            ->andWhere('promotion.isDeleted = :isDeleted')
            ->setParameter('isDeleted', false)
            ->getQuery()
            ->execute();
    }

    public function findAllDeletedPromotions()
    {
        return $this->createQueryBuilder('promotion')
            ->andWhere('promotion.isDeleted = :isDeleted')
            ->setParameter('isDeleted', true)
            ->getQuery()
            ->execute();
    }

    /**
     * @return Promotion
     */
    public function fetchBiggestPromotion(Product $product){
        $today = new \DateTime();

        $q =  $this->createQueryBuilder("promotion")
            ->join("promotion.products", "product")
            ->andWhere("product = :prod")
            ->setParameter("prod", $product)
            ->andWhere("promotion.startDate <= :now")
            ->andWhere("promotion.endDate >= :now")
            ->setParameter("now", $today)
            ->orderBy("promotion.discount", "desc")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $q;
    }
}
