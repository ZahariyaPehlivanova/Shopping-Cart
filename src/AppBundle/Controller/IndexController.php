<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function sidebarMenuAction()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findAllActiveCategories();

        foreach ($categories as $category){
            $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProductsByCategory($category);
            $category->setActiveProducts($products);
        }

        return $this->render("categories_menu.html.twig", [
            "categories" => $categories
        ]);
    }
}
