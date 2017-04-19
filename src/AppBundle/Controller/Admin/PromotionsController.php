<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Promotion;
use AppBundle\Form\Admin\AddAndEditPromotionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Category;
use AppBundle\Form\Admin\AddAndEditCategoryType;
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
        $activePromotions = $this->getDoctrine()->getRepository(Promotion::class)->findAllActivePromotions();
        $deletedPromotions = $this->getDoctrine()->getRepository(Promotion::class)->findAllDeletedPromotions();

        return $this->render(":admin/promotions:all_promotions.html.twig", [
            "activePromotions" => $activePromotions,
            "deletedPromotions" => $deletedPromotions
        ]);
    }

    /**
     * @Route("admin/add/promotion", name="admin_add_promotion")
     * @return Response
     */
    public function addPromotionAction(Request $request)
    {
        $promotion = new Promotion();
        $form = $this->createForm(AddAndEditPromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($promotion);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Promotion was created successfully!');

            return $this->redirectToRoute('get_all_promotions');
        }

        return $this->render(':admin/promotions:admin_promotion_add.html.twig', [
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
        $form = $this->createForm(AddAndEditPromotionType::class, $promotion);
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
