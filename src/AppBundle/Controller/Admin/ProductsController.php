<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Form\AddProductType;
use AppBundle\Form\ProductEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
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
