<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\Promotion;
use AppBundle\Form\Admin\AddAndEditAllProductsPromotionType;
use AppBundle\Form\Admin\AddAndEditCategoryPromotionType;
use AppBundle\Form\Admin\AddAndEditProductPromotionType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PromotionsController extends Controller
{
    /**
     * @Route("admin/all/promotions", name="get_all_promotions")
     */
    public function allPromotionsAction()
    {
        $repository = $this->getDoctrine()->getRepository(Promotion::class);
        $activePromotions = $repository->findAllActivePromotions();
        $deletedPromotions = $repository->findAllDeletedPromotions();
        $allProducts = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProducts();

        return $this->render(":admin/promotions:all_promotions.html.twig", [
            "activePromotions" => $activePromotions,
            "deletedPromotions" => $deletedPromotions,
            "allProducts" => $allProducts
        ]);
    }

    /**
     * @Route("admin/add/productPromotion", name="admin_add_product_promotion")
     * @return Response
     */
    public function addProductPromotionAction(Request $request)
    {
        $promotion = new Promotion();
        $form = $this->createForm(AddAndEditProductPromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var Product[]|ArrayCollection $products */
            $products = $promotion->getProducts();

            foreach ($products as $product){
                $oldPrice = $product->getPrice();
                $discountInPercentage = $promotion->getDiscount() / 100;
                $newPrice = $oldPrice - ($oldPrice * $discountInPercentage);

                if ($product->getPromotionPrice() > $newPrice) {
                    $product->setPromotionPrice($newPrice);
                    $productOldPromotion = $product->getPromotions()[0];
                    $product->getPromotions()->removeElement($productOldPromotion);
                }
                $product->addPromotion($promotion);
                $em->persist($product);
            }

            $em->persist($promotion);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Promotion was created successfully!');

            return $this->redirectToRoute('get_all_promotions');
        }

        return $this->render('admin/promotions/admin_product_promotion_add.html.twig', [
                'promotion' => $promotion,
                'form'    => $form->createView(),
            ]
        );
    }

    /**
     * @Route("admin/edit/productPromotion/{id}", name="admin_edit_product_promotion")
     */
    public function editProductPromotionAction(Promotion $promotion, Request $request)
    {
        $form = $this->createForm(AddAndEditProductPromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var Product[]|ArrayCollection $products */
            $products = $promotion->getProducts();

            foreach ($products as $product){
                $oldPrice = $product->getPrice();
                $discountInPercentage = $promotion->getDiscount() / 100;
                $newPrice = $oldPrice - ($oldPrice * $discountInPercentage);

                if ($product->getPromotionPrice() > $newPrice) {
                    $product->setPromotionPrice($newPrice);
                    $productOldPromotion = $product->getPromotions()[0];
                    $product->getPromotions()->removeElement($productOldPromotion);
                }
                if(!$product->getPromotions()->contains($promotion)){
                    $product->addPromotion($promotion);
                }

                $em->persist($product);
            }

            $em->persist($promotion);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Promotion was created successfully!');

            return $this->redirectToRoute('get_all_promotions');
        }

        return $this->render('admin/promotions/admin_product_promotion_edit.html.twig', [
                'promotion' => $promotion,
                'form'    => $form->createView(),
            ]
        );
    }

    /**
     * @Route("admin/delete/productPromotion/{id}", name="admin_delete_product_promotion")
     */
    public function deleteProductPromotionAction(Promotion $promotion)
    {
        $em = $this->getDoctrine()->getManager();
            /** @var Product[]|ArrayCollection $products */
            $products = $promotion->getProducts();
            foreach ($products as $product){
                $realPrice = $product->getPrice();
                $product->setPromotionPrice($realPrice);
                $product->getPromotions()->removeElement($promotion);

                $em->persist($product);
            }

        $promotion->setIsDeleted(true);
        $em->persist($promotion);
        $em->flush();

        $this->addFlash("success", "Promotion {$promotion->getName()} deleted successfully!");

        return $this->redirectToRoute("get_all_promotions");
    }

    /**
     * @Route("admin/add/categoryPromotion", name="admin_add_category_promotion")
     * @return Response
     */
    public function addCategoryPromotionAction(Request $request)
    {
        $promotion = new Promotion();
        $form = $this->createForm(AddAndEditCategoryPromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            /** @var Category[]|ArrayCollection $categories */
            $categories = $promotion->getCategories();
            foreach ($categories as $category)
            {
                $category->addPromotion($promotion);

                /** @var Product[]|ArrayCollection $products */
                $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProductsByCategory($category);
                foreach ($products as $product){
                    $oldPrice = $product->getPrice();
                    $discountInPercentage = $promotion->getDiscount() / 100;
                    $newPrice = $oldPrice - ($oldPrice * $discountInPercentage);

                    if ($product->getPromotionPrice() > $newPrice) {
                        $product->setPromotionPrice($newPrice);
                        $productOldPromotion = $product->getPromotions()[0];
                        $product->getPromotions()->removeElement($productOldPromotion);
                    }
                    $em->persist($product);
                }
                $em->persist($category);
            }

            $em->persist($promotion);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Promotion was created successfully!');

            return $this->redirectToRoute('get_all_promotions');
        }

        return $this->render(':admin/promotions:admin_category_promotion_add.html.twig', [
                'promotion' => $promotion,
                'form'    => $form->createView(),
            ]
        );
    }

    /**
     * @Route("admin/delete/categoryPromotion/{id}", name="admin_delete_category_promotion")
     */
    public function deleteCategoryPromotionAction(Promotion $promotion)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Category[]|ArrayCollection $categories */
        $categories = $promotion->getCategories();
        foreach ($categories as $category){
            /** @var Product[]|ArrayCollection $products */
            $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProductsByCategory($category);
            foreach ($products as $product){
                $realPrice = $product->getPrice();
                $product->setPromotionPrice($realPrice);
                $em->persist($product);
            }
            $category->getPromotions()->removeElement($promotion);
            $em->persist($category);
        }
        $promotion->setIsDeleted(true);
        $em->persist($promotion);
        $em->flush();

        $this->addFlash("success", "Promotion {$promotion->getName()} deleted successfully!");

        return $this->redirectToRoute("get_all_promotions");
    }

    /**
     * @Route("admin/add/allProductsPromotion", name="admin_add_allProducts_promotion")
     * @return Response
     */
    public function addAllProductsPromotionAction(Request $request)
    {
        $promotion = new Promotion();
        $form = $this->createForm(AddAndEditAllProductsPromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            /** @var Product[]|ArrayCollection $products */
            $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProducts();
            foreach ($products as $product){

                $oldPrice = $product->getPrice();
                $discountInPercentage = $promotion->getDiscount() / 100;
                $newPrice = $oldPrice - ($oldPrice * $discountInPercentage);

                if ($product->getPromotionPrice() > $newPrice) {
                    $product->setPromotionPrice($newPrice);
                    $productOldPromotion = $product->getPromotions()[0];
                    $product->getPromotions()->removeElement($productOldPromotion);
                }
                $product->addPromotion($promotion);
                $em->persist($product);
            }

            $em->persist($promotion);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Promotion was created successfully!');

            return $this->redirectToRoute('get_all_promotions');
        }

        return $this->render('admin/promotions/admin_all_products_promotion_add.html.twig', [
                'promotion' => $promotion,
                'form'    => $form->createView(),
            ]
        );
    }

    /**
     * @Route("admin/edit/allProductsPromotion", name="admin_edit_allProducts_promotion")
     * @return Response
     */
    public function editAllProductsPromotionAction(Promotion $promotion, Request $request)
    {
        $promotion = new Promotion();
        $form = $this->createForm(AddAndEditAllProductsPromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            /** @var Product[]|ArrayCollection $products */
            $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProducts();
            foreach ($products as $product){

                $oldPrice = $product->getPrice();
                $discountInPercentage = $promotion->getDiscount() / 100;
                $newPrice = $oldPrice - ($oldPrice * $discountInPercentage);

                if ($product->getPromotionPrice() > $newPrice) {
                    $product->setPromotionPrice($newPrice);
                    $productOldPromotion = $product->getPromotions()[0];
                    $product->getPromotions()->removeElement($productOldPromotion);
                }
                $product->addPromotion($promotion);
                $em->persist($product);
            }

            $em->persist($promotion);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Promotion was created successfully!');

            return $this->redirectToRoute('get_all_promotions');
        }

        return $this->render('admin/promotions/admin_all_products_promotion_add.html.twig', [
                'promotion' => $promotion,
                'form'    => $form->createView(),
            ]
        );
    }

   /**
     * @Route("admin/delete/allProductsPromotion/{id}", name="admin_delete_allProducts_promotion")
     */
    public function deleteAllProductsPromotionAction(Promotion $promotion)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Product[]|ArrayCollection $products */
        $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProducts();
        foreach ($products as $product){
            $realPrice = $product->getPrice();
            $product->setPromotionPrice($realPrice);
            $product->getPromotions()->removeElement($promotion);
            $em->persist($product);
        }
        $promotion->setIsDeleted(true);

        $em->persist($promotion);
        $em->flush();

        $this->addFlash("success", "Promotion {$promotion->getName()} deleted successfully!");

        return $this->redirectToRoute("get_all_promotions");
    }

    /**
     * @Route("admin/restore/promotion/{id}", name="admin_restore_promotion")
     */
    public function restorePromotionAction(Promotion $promotion)
    {
        $promotion->setIsDeleted(false);
        $em = $this->getDoctrine()->getManager();
        $em->persist($promotion);
        $em->flush();

        $this->addFlash("success", "Promotion {$promotion->getName()} restored successfully!");

        return $this->redirectToRoute("get_all_promotions");
    }
}
