<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\ProductsOrder;
use AppBundle\Form\Admin\ChangeOrderType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ProductsOrderController extends Controller
{
    /**
     * @Route("admin/products/changeOrder", name="changeOrder")
     */
    public function changeOrderProductsAction(Request $request)
    {
        if(!$this->isAuthenticated()){
            $this->addFlash("error", "You are not allowed to reorder the products!");
            return $this->redirectToRoute("allProducts");
        }
        $order = new ProductsOrder();
        $form = $this->createForm(ChangeOrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $previousOrders = $this->getDoctrine()->getRepository(ProductsOrder::class)->findAll();

            $em = $this->getDoctrine()->getManager();

            if($previousOrders){
                foreach ($previousOrders as $previousOrder){
                    $em->remove($previousOrder);
                }
            }
            $em->flush();

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

    private function isAuthenticated(){
        $isAdmin = $this->isGranted('ROLE_ADMIN', $this->getUser());
        $isEditor = $this->isGranted('ROLE_EDITOR', $this->getUser());
        return $isAdmin || $isEditor;
    }
}
