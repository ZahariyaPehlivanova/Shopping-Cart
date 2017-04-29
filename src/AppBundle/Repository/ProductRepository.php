<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;

class ProductRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllActiveProducts($orderBy = null, $criteria = null)
    {
        if($orderBy === "price"){
            return $this->createQueryBuilder('product')
                ->join('product.category', 'category')
                ->andWhere('product.quantity > 0')
                ->andWhere('category.isDeleted = :isDeleted')
                ->setParameter('isDeleted', false)
                ->orderBy("product.promotionPrice", $criteria)
                ->getQuery()
                ->execute();
        }
        else if($orderBy === "rating"){
            return $this->createQueryBuilder('product')
                ->join('product.category', 'category')
                ->andWhere('product.quantity > 0')
                ->andWhere('category.isDeleted = :isDeleted')
                ->setParameter('isDeleted', false)
                ->orderBy("product.rating", $criteria)
                ->getQuery()
                ->execute();
        }
        else if($orderBy === "addedOn"){
            return $this->createQueryBuilder('product')
                ->join('product.category', 'category')
                ->andWhere('product.quantity > 0')
                ->andWhere('category.isDeleted = :isDeleted')
                ->setParameter('isDeleted', false)
                ->orderBy("product.createdOn", $criteria)
                ->getQuery()
                ->execute();
        }
        else {
            return $this->createQueryBuilder('product')
                ->join('product.category', 'category')
                ->andWhere('product.quantity > 0')
                ->andWhere('category.isDeleted = :isDeleted')
                ->setParameter('isDeleted', false)
                ->orderBy("product.promotionPrice", "ASC")
                ->getQuery()
                ->execute();
        }
    }

    public function findAllActiveProductsByCategory(Category $category,$orderBy = null, $criteria = null)
    {
        if($orderBy === "price"){
            return $this->createQueryBuilder('product')
                ->join('product.category', 'category')
                ->andWhere('product.quantity > 0')
                ->andWhere('product.category = :category')
                ->setParameter('category', $category)
                ->andWhere('category.isDeleted = :isDeleted')
                ->setParameter('isDeleted', false)
                ->orderBy("product.promotionPrice", $criteria)
                ->getQuery()
                ->execute();
        }
        else if($orderBy === "rating"){
            return $this->createQueryBuilder('product')
                ->join('product.category', 'category')
                ->andWhere('product.quantity > 0')
                ->andWhere('product.category = :category')
                ->setParameter('category', $category)
                ->andWhere('category.isDeleted = :isDeleted')
                ->setParameter('isDeleted', false)
                ->orderBy("product.rating", $criteria)
                ->getQuery()
                ->execute();
        }
        else if($orderBy === "addedOn"){
            return $this->createQueryBuilder('product')
                ->join('product.category', 'category')
                ->andWhere('product.quantity > 0')
                ->andWhere('product.category = :category')
                ->setParameter('category', $category)
                ->andWhere('category.isDeleted = :isDeleted')
                ->setParameter('isDeleted', false)
                ->orderBy("product.createdOn", $criteria)
                ->getQuery()
                ->execute();
        }
        else {
            return $this->createQueryBuilder('product')
                ->join('product.category', 'category')
                ->andWhere('product.quantity > 0')
                ->andWhere('product.category = :category')
                ->setParameter('category', $category)
                ->andWhere('category.isDeleted = :isDeleted')
                ->setParameter('isDeleted', false)
                ->orderBy("product.promotionPrice", "ASC")
                ->getQuery()
                ->execute();
        }
    }

    public function findAllProductsByAuthor($username)
    {
        return $this->createQueryBuilder('product')
            ->join('product.seller', 'seller')
            ->andWhere('seller.username = :author')
            ->setParameter('author', $username)
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
