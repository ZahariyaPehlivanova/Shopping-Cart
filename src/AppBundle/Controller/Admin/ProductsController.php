<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Product;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends Controller
{
    /**
     * @Route("admin/products/outOfStock", name="outOfStockProducts")
     */
    public function outOfStockProductsAction()
    {
        if(!$this->isAuthenticated()){
            $this->addFlash("error", "You are not allowed to see this products!");
            return $this->redirectToRoute("allProducts");
        }
        $products = $this->getDoctrine()->getRepository(Product::class)->findAllOutOfStockProducts();

        return $this->render("admin/products/out_of_stock_products.html.twig", [
            "products" => $products
        ]);
    }

    private function isAuthenticated(){
        $isAdmin = $this->isGranted('ROLE_ADMIN', $this->getUser());
        $isEditor = $this->isGranted('ROLE_EDITOR', $this->getUser());
        return $isAdmin || $isEditor;
    }
}
