<?php

namespace AppBundle\Controller\Admin;

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
        $repository =  $this->getDoctrine()->getRepository(Promotion::class);
        $activePromotions = $repository->findAllActivePromotions();
        $deletedPromotions = $repository->findAllDeletedPromotions();

        return $this->render(":admin/promotions:all_promotions.html.twig", [
            "activePromotions" => $activePromotions,
            "deletedPromotions" => $deletedPromotions
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

                $product->addPromotion($promotion);
                $oldPrice = $product->getPrice();
                $discountInPercentage = $promotion->getDiscount() / 100;
                $newPrice = $oldPrice - ($oldPrice * $discountInPercentage);
                $product->setPrice($newPrice);
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
            $categories = $promotion->getCategories();
            foreach ($categories as $category)
            {
                $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProductsByCategory($category);
                foreach ($products as $product){
                    $oldPrice = $product->getPrice();
                    $discountInPercentage = $promotion->getDiscount() / 100;
                    $newPrice = $oldPrice - ($oldPrice * $discountInPercentage);
                    $product->setPrice($newPrice);
                    $em->persist($product);
                }
            }

            $em->flush();
            $em->persist($promotion);

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
            $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveProducts();
            foreach ($products as $product){
                $oldPrice = $product->getPrice();
                $discountInPercentage = $promotion->getDiscount() / 100;
                $newPrice = $oldPrice - ($oldPrice * $discountInPercentage);
                $product->setPrice($newPrice);
                $em->persist($product);
            }

            $em->flush();
            $em->persist($promotion);

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
     * @Route("admin/edit/promotion/{id}", name="admin_edit_promotion")
     * @return Response
     */
    public function editPromotionAction(Promotion $promotion, Request $request)
    {
        $form = $this->createForm(AddAndEditProductPromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($promotion);
            $em->flush();

            $this->addFlash("success", "Promotion {$promotion->getName()} updated successfully!");

            return $this->redirectToRoute("get_all_promotions");
        }

        return $this->render(":admin/promotions:admin_promotion_edit.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("admin/delete/promotion/{id}", name="admin_delete_promotion")
     */
    public function deletePromotionAction(Promotion $promotion)
    {
        $promotion->setIsDeleted(true);
        $em = $this->getDoctrine()->getManager();
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
