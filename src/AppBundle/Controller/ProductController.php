<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Form\AddProductType;
use AppBundle\Form\ProductEditType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class ProductController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/", name="allProducts")
     */
    public function listAllProductsAction(Request $request)
    {
        /** @var Product[]|ArrayCollection $products */
        $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProducts();

        $calc = $this->get('price_calculator');
        $em = $this->getDoctrine()->getManager();
        foreach ($products as $product){
            $promoPrice = $calc->calcPrice($product);
            $product->setPromotionPrice($promoPrice);
            $em->persist($product);
        }
        $em->flush();

        $pager = $this->get('knp_paginator');
        $products = $pager->paginate(
            $products,
            $request->query->getInt('page', 1),
            6
        );
        return $this->render(":product:all.html.twig", [
            "products" => $products
        ]);
    }

    /**
     * @Route("/category/{id}", name="products_by_category")
     * @Method("GET")
     * @param Category $category
     * @return Response
     */
    public function productsByCategoryAction(Category $category, Request $request)
    {
        $pager = $this->get('knp_paginator');
        $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProductsByCategory($category);
       // $products = $this->checkPromotions($products);

        $products = $pager->paginate(
            $products,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render("product/all_by_category.html.twig", [
            "products" => $products,
            "category" => $category
        ]);
    }

    /**
     * @Route("product/details/{id}", name="product_details")
     * @return Response
     */
    public function productDetailsAction(Product $product)
    {
        return $this->render(":product:details.html.twig", [
            "product" => $product
        ]);
    }

    /**
     * @Route("product/add", name="add_product")
     * @Method("GET")
     * @Security(expression="is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function addProductAction()
    {
        $form = $this->createForm(AddProductType::class);
        return $this->render('product/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("product/add", name="add_product_process")
     * @Method("POST")
     * @Security(expression="is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function addProductProcessAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(AddProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedOn(new \DateTime());
            $product->setUpdatedOn(new \DateTime());
            $user = $this->getUser();
            $product->setSeller($user);
            $product->setPromotionPrice($product->getPrice());
            $file = $product->getImageForm();

            if (!$file) {
                $form->get('image_form')->addError(new FormError('Image is required'));
            } else {
                $filename = md5($product->getName() . '' . $product->getCreatedOn()->format('Y-m-d H:i:s'));

                $file->move(
                    $this->get('kernel')->getRootDir() . '/../web/images/product/',
                    $filename
                );

                $product->setImage($filename);

                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'Product is created successfully!');

                return $this->redirectToRoute('allProducts');
            }
        }

        return $this->render('product/add.html.twig', [
            'product' => $product,
            'form'    => $form->createView(),
            ]
        );
    }

    /**
     * @Route("product/edit/{id}", name="product_edit")
     * @Security(expression="is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function editProductAction(Product $product, Request $request)
    {
        if(!$this->isAuthenticated($product)){
            $this->addFlash("error", "You are not allowed to edit this product!");
            return $this->redirectToRoute("allProducts");
        }
        $form = $this->createForm(ProductEditType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUpdatedOn(new \DateTime());
            $file = $product->getImageForm();

            if (!$file) {
                $form->get('image_form')->addError(new FormError('Image is required'));
            } else {
                $filename = md5($product->getName() . '' . $product->getCreatedOn()->format('Y-m-d H:i:s'));

                $file->move(
                    $this->get('kernel')->getRootDir() . '/../web/images/product/',
                    $filename
                );

                $product->setImage($filename);

                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();

                $this->addFlash("success", "Product {$product->getName()} updated successfully!");

                return $this->redirectToRoute("product_details", ['id' => $product->getId()]);
            }
        }

        return $this->render(":product:edit.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("product/delete/{id}", name="product_delete")
     * @Security(expression="is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function deleteProductAction(Product $product)
    {
       if(!$this->isAuthenticated($product)){
           $this->addFlash("error", "You are not allowed to delete this product!");
           return $this->redirectToRoute("allProducts");
       }
        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        $this->addFlash("success", "Product {$product->getName()} deleted successfully!");

        return $this->redirectToRoute("allProducts");
    }

    private function isAuthenticated(Product $product){
        $isAdmin = $this->isGranted('ROLE_ADMIN', $this->getUser());
        $isEditor = $this->isGranted('ROLE_EDITOR', $this->getUser());
        $isSeller = $product->getSeller()->getId() == $this->getUser()->getId();
        return $isAdmin || $isEditor || $isSeller;
    }

//    private function checkPromotions($products){
//        $em = $this->getDoctrine()->getManager();
//        foreach ($products as $product){
//            $promotions = $product->getPromotions();
//            if(count($promotions) > 0){
//                foreach ($promotions as $promotion) {
//                    if ($promotion->getIsDeleted() == false) {
//                        $now = new DateTime();
//                        if($promotion->getDuration() < $now){
//                            $oldPrice = $product->getPrice();
//                            $discountInPercentage = $promotion->getDiscount() / 100;
//                            $newPrice = $oldPrice + ($oldPrice * $discountInPercentage);
//                            $product->setPrice($newPrice);
//                            $promotion->setIsDeleted(true);
//
//                            $em->persist($promotion);
//                            $em->persist($product);
//                            $em->flush();
//                        }
//                    }
//                }
//            }
//        }
//        return $products;
//    }
}

