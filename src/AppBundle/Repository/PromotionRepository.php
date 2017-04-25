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
        $qb = $this->createQueryBuilder('p');
        $today = new \DateTime();

//       return $qb->select('p')
//            ->where($qb->expr()->lte('p.startDate', ':today'))
//            ->andWhere($qb->expr()->gte('p.endDate', ':today'))
//            ->setParameter(':today', $today->format('Y-m-d'))
//            ->andWhere('p.isDeleted = :isDeleted')
//            ->setParameter('isDeleted', false)
//            ->andWhere($qb->expr()->in('p.products',':product'))
//            ->setParameter(':product', $product)
//            ->orderBy('p.discount', 'DESC')
//            ->setMaxResults(1)
//            ->getQuery()
//           ->execute();

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
