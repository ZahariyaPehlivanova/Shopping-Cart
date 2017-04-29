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
        if(!$this->isAuthenticated()){
            $this->addFlash("error", "You are not allowed to see the promotions!");
            return $this->redirectToRoute("allProducts");
        }

        $repository = $this->getDoctrine()->getRepository(Promotion::class);
        $activePromotions = $repository->findAllActivePromotions();
        $allProducts = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProducts();

        return $this->render(":admin/promotions:all_promotions.html.twig", [
            "activePromotions" => $activePromotions,
            "allProducts" => $allProducts
        ]);
    }

    /**
     * @Route("admin/add/productPromotion", name="admin_add_product_promotion")
     * @return Response
     */
    public function addProductPromotionAction(Request $request)
    {
        if(!$this->isAuthenticated()){
            $this->addFlash("error", "You are not allowed to add promotions!");
            return $this->redirectToRoute("allProducts");
        }
        $promotion = new Promotion();
        $form = $this->createForm(AddAndEditProductPromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var Product[]|ArrayCollection $products */
            $products = $promotion->getProducts();

            $this->addPromoForEachProduct($em, $products, $promotion);

            $promotion->setIsProductPromo(true);
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
        if(!$this->isAuthenticated()){
            $this->addFlash("error", "You are not allowed to edit promotions!");
            return $this->redirectToRoute("allProducts");
        }
        $form = $this->createForm(AddAndEditProductPromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var Product[]|ArrayCollection $products */
            $products = $promotion->getProducts();

            $this->addPromoForEachProduct($em, $products, $promotion);

            $em->persist($promotion);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Promotion edited successfully!');

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
        if(!$this->isAuthenticated()){
            $this->addFlash("error", "You are not allowed to delete promotions!");
            return $this->redirectToRoute("allProducts");
        }
        $em = $this->getDoctrine()->getManager();
            /** @var Product[]|ArrayCollection $products */
            $products = $promotion->getProducts();
            foreach ($products as $product){
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
        if(!$this->isAuthenticated()){
            $this->addFlash("error", "You are not allowed to add promotions!");
            return $this->redirectToRoute("allProducts");
        }
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

                $this->addPromoForEachProduct($em, $products, $promotion);

                $em->persist($category);
            }
            $promotion->setIsCategoryPromo(true);
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
     * @Route("admin/edit/categoryPromotion/{id}", name="admin_edit_category_promotion")
     * @return Response
     */
    public function editCategoryPromotionAction(Promotion $promotion, Request $request)
    {
        if(!$this->isAuthenticated()){
            $this->addFlash("error", "You are not allowed to edit promotions!");
            return $this->redirectToRoute("allProducts");
        }
        $form = $this->createForm(AddAndEditCategoryPromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $categories = $promotion->getCategories();

            foreach ($categories as $category)
            {
                if(!$category->getPromotions()->contains($promotion)){
                    $category->addPromotion($promotion);
                }

                /** @var Product[]|ArrayCollection $products */
                $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProductsByCategory($category);

                $this->addPromoForEachProduct($em, $products, $promotion);

                $em->persist($category);
            }

            $em->persist($promotion);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Promotion edited successfully!');

            return $this->redirectToRoute('get_all_promotions');
        }

        return $this->render(':admin/promotions:admin_category_promotion_edit.html.twig', [
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
        if(!$this->isAuthenticated()){
            $this->addFlash("error", "You are not allowed to delete promotions!");
            return $this->redirectToRoute("allProducts");
        }
        $em = $this->getDoctrine()->getManager();
        /** @var Category[]|ArrayCollection $categories */
        $categories = $promotion->getCategories();
        foreach ($categories as $category){
            /** @var Product[]|ArrayCollection $products */
            $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProductsByCategory($category);
            foreach ($products as $product){
                $product->getPromotions()->removeElement($promotion);
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
        if(!$this->isAuthenticated()){
            $this->addFlash("error", "You are not allowed to add promotions!");
            return $this->redirectToRoute("allProducts");
        }
        $promotion = new Promotion();
        $form = $this->createForm(AddAndEditAllProductsPromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            /** @var Product[]|ArrayCollection $products */
            $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProducts();

            $this->addPromoForEachProduct($em, $products, $promotion);

            $promotion->setIsAllProductsPromo(true);
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
     * @Route("admin/edit/allProductsPromotion/{id}", name="admin_edit_allProducts_promotion")
     * @return Response
     */
    public function editAllProductsPromotionAction(Promotion $promotion, Request $request)
    {
        if(!$this->isAuthenticated()){
            $this->addFlash("error", "You are not allowed to edit promotions!");
            return $this->redirectToRoute("allProducts");
        }
        $form = $this->createForm(AddAndEditAllProductsPromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            /** @var Product[]|ArrayCollection $products */
            $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProducts();

            $this->addPromoForEachProduct($em, $products, $promotion);

            $em->persist($promotion);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Promotion edited successfully!');

            return $this->redirectToRoute('get_all_promotions');
        }

        return $this->render('admin/promotions/admin_all_products_promotion_edit.html.twig', [
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
        if(!$this->isAuthenticated()){
            $this->addFlash("error", "You are not allowed to delete promotions!");
            return $this->redirectToRoute("allProducts");
        }
        $em = $this->getDoctrine()->getManager();
        /** @var Product[]|ArrayCollection $products */
        $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProducts();
        foreach ($products as $product){
            $product->getPromotions()->removeElement($promotion);
            $em->persist($product);
        }
        $promotion->setIsDeleted(true);

        $em->persist($promotion);
        $em->flush();

        $this->addFlash("success", "Promotion {$promotion->getName()} deleted successfully!");

        return $this->redirectToRoute("get_all_promotions");
    }

   private function addPromoForEachProduct($em, $products, $promotion){
        foreach ($products as $product){
            if(!$product->getPromotions()->contains($promotion)){
                $product->addPromotion($promotion);
            }
            $em->persist($product);
        }
    }

    private function isAuthenticated(){
        $isAdmin = $this->isGranted('ROLE_ADMIN', $this->getUser());
        $isEditor = $this->isGranted('ROLE_EDITOR', $this->getUser());
        return $isAdmin || $isEditor;
    }
}
