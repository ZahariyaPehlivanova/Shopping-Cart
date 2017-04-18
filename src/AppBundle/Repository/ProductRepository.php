<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;

class ProductRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllActiveProducts()
    {
        return $this->createQueryBuilder('product')
            ->andWhere('product.quantity > 0')
            ->getQuery()
            ->execute();
    }

    public function findAllActiveProductsByCategory(Category $category)
    {
        return $this->createQueryBuilder('product')
            ->andWhere('product.category = :category')
            ->setParameter('category', $category)
            ->andWhere('product.quantity > 0')
            ->getQuery()
            ->execute();
    }

    public function findAllOutOfStockProducts()
    {
        return $this->createQueryBuilder('product')
            ->andWhere('product.quantity <= 0')
            ->getQuery()
            ->execute();
    }
}
