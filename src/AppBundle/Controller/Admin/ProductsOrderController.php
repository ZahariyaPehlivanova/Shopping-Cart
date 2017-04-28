<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\ProductsOrder;
use AppBundle\Form\Admin\ChangeOrderType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\Promotion;
use AppBundle\Form\Admin\AddAndEditAllProductsPromotionType;
use AppBundle\Form\Admin\AddAndEditCategoryPromotionType;
use AppBundle\Form\Admin\AddAndEditProductPromotionType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsOrderController extends Controller
{
    /**
     * @Route("admin/products/changeOrder", name="changeOrder")
     */
    public function changeOrderProductsAction(Request $request)
    {
        $order = new ProductsOrder();
        $form = $this->createForm(ChangeOrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $previousOrder = $this->getDoctrine()->getRepository(ProductsOrder::class)->findAll();

            $em = $this->getDoctrine()->getManager();

            if($previousOrder){
                foreach ($previousOrder as $order){
                    $em->remove($order);
                }
            }

            $em->persist($order);

            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'The order was changed successfully!');

            return $this->redirectToRoute('allProducts');
        }

        return $this->render(':admin/products:order_products.html.twig', [
                'form'    => $form->createView(),
            ]
        );
    }
}
