<?php

namespace AppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{
    /**
     * @Route("cart", name="user_cart")
     *
     * @return Response
     * @Security("is_authenticated()")
     */
    public function viewCartAction(Request $request)
    {
        if(!$this->hasUserRole()){
            $this->addFlash("danger", "You are admin/editor. You don't have a cart!");
            return $this->redirectToRoute("user_profile", ["id" => $this->getUser()->getId()]);
        }
        $user = $this->getUser();
        $products = $user->getProducts();

        $pager = $this->get('knp_paginator');
        $products = $pager->paginate(
            $products,
            $request->query->getInt('page', 1),
            6
        );
        $totalBill = $this->getTotalBill($products);
        return $this->render('cart/view_cart.html.twig', ['products' => $products, 'bill' => $totalBill,'user' => $user]);
    }

    /**
     * @Route("cart/add/{id}", name="cart_add_product")
     *
     * @Security("is_authenticated()")
     * @return Response
     */
    public function addToCartAction(Product $product)
    {
        if($this->hasUserRole()){
            $this->addFlash("danger", "You are admin/editor. You don't have a cart!");
            return $this->redirectToRoute("user_profile", ["id" => $this->getUser()->getId()]);
        }

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
     * @Security("is_authenticated()")
     * @return Response
     */
    public function removeFromCartAction(Product $product)
    {
        if($this->hasUserRole()){
            $this->addFlash("danger", "You are admin/editor. You don't have a cart!");
            return $this->redirectToRoute("user_profile", ["id" => $this->getUser()->getId()]);
        }

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
     * @Security("is_authenticated()")
     * @return Response
     */
    public function checkoutCartAction()
    {
        if($this->hasUserRole()){
            $this->addFlash("danger", "You are admin/editor. You don't have a cart!");
            return $this->redirectToRoute("user_profile", ["id" => $this->getUser()->getId()]);
        }
        $user = $this->getUser();

        /** @var Product[]|ArrayCollection $products */
        $products = $user->getProducts();
        $totalBill = $this->getTotalBill($products);

        if($user->getInitialCash() >= $totalBill) {
            $em = $this->getDoctrine()->getManager();
            foreach ($products as $product){
                $seller = $product->getSeller();

                $sellerOldMoney = $seller->getInitialCash();
                $seller->setInitialCash($sellerOldMoney + $product->getPromotionPrice());
                $em->persist($seller);
            }

            $cash = $user->getInitialCash();
            $user->setInitialCash($cash -= $totalBill);

            $user->setProducts($this->cleanCart($products));
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "The order was made.");
            return $this->redirectToRoute("allProducts");
        }
        else{
            $needed = $totalBill - $user->getInitialCash();
            $this->addFlash("error", "You don't have enough cash to buy the products. You need $needed lv. more. Please, remove some of them and try again.");
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

    private function hasUserRole(){
        return $this->isGranted('ROLE_USER', $this->getUser());
    }
}
