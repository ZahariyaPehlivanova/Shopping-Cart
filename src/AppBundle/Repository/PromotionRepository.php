<?php

namespace AppBundle\Repository;

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
}
