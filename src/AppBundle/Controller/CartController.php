<?php

namespace AppBundle\Controller;

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

class CartController extends Controller
{
    /**
     * @Route("cart", name="user_cart")
     *
     * @return Response
     */
    public function viewCartAction()
    {
        $user = $this->getUser();
        $products = $user->getProducts();
        return $this->render('cart/view_cart.html.twig', ['products' => $products]);
    }

    /**
     * @Route("cart/add/{id}", name="cart_add_product")
     *
     * @return Response
     */
    public function addToCartAction(Product $product)
    {
        $user = $this->getUser();
        if($user->getProducts()->contains($product)){
            $this->addFlash("error", "Product already exists in your cart.");
            return $this->redirectToRoute("allProducts");
        }
        if($user->getInitialCash() >= $product->getPrice()) {
            $em = $this->getDoctrine()->getManager();
            $user->getProducts()->add($product);
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "Product added to your cart.");
            return $this->redirectToRoute("user_cart");
        }
        else{
            $this->addFlash("error", "You don't have enough cash to buy this product.");
            return $this->redirectToRoute("product_details", ['id'=>$product->getId()]);
        }
    }
}
