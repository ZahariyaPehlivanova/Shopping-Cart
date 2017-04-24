<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Product;
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
        $totalBill = $this->getTotalBill($products);
        return $this->render('cart/view_cart.html.twig', ['products' => $products, 'bill' => $totalBill,'user' => $user]);
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
            $this->addFlash("danger", "You have this product in your cart.");
            return $this->redirectToRoute("user_cart");
        }

        $em = $this->getDoctrine()->getManager();

        $currQuantity = $product->getQuantity();
        $product->setQuantity($currQuantity -= 1);

        $user->getProducts()->add($product);

        $em->persist($user);
        $em->persist($product);
        $em->flush();

        $this->addFlash("success", "Product added to your cart.");
        return $this->redirectToRoute("user_cart");
    }

    /**
     * @Route("cart/remove/{id}", name="cart_remove_product")
     *
     * @return Response
     */
    public function removeFromCartAction(Product $product)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $user->getProducts()->removeElement($product);

        $quantity = $product->getQuantity();
        $product->setQuantity($quantity += 1);

        $em->persist($user);
        $em->persist($product);
        $em->flush();

        $productName = $product->getName();
        $this->addFlash("success", "Product $productName removed from your cart.");
        return $this->redirectToRoute("user_cart");
    }

    /**
     * @Route("cart/checkout", name="cart_checkout")
     *
     * @return Response
     */
    public function checkoutCartAction()
    {
        $user = $this->getUser();

        $products = $user->getProducts();
        $totalBill = $this->getTotalBill($products);

        if($user->getInitialCash() >= $totalBill) {
            $em = $this->getDoctrine()->getManager();
            $cash = $user->getInitialCash();
            $user->setInitialCash($cash -= $totalBill);

            $user->setProducts($this->cleanCart($products));
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "The order was made.");
            return $this->redirectToRoute("allProducts");
        }
        else{
            $this->addFlash("error", "You don't have enough cash to buy the products. Please, remove some of them and try again.");
            return $this->redirectToRoute("user_cart");
        }
    }

    private function getTotalBill($products){
        $sum = 0;
        foreach ($products as $product){
            $sum += $product->getPromotionPrice();
        }
        return $sum;
    }

    private function cleanCart($products){
        foreach ($products as $product){
            $products->removeElement($product);
        }
        return $products;
    }
}
