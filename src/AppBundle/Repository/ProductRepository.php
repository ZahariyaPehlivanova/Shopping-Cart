<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;

class ProductRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllProductsByCategory(Category $category)
    {
        return $this->createQueryBuilder('product')
            ->andWhere('product.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->execute();
    }
}
