<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\AddProductType;
use AppBundle\Form\ProductEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/", name="homepage")
     */
    public function listAllProductsAction()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render(":product:all.html.twig", [
            "products" => $products
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

                return $this->redirectToRoute('homepage');
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
     * @return Response
     */
    public function editProductAction(Product $product, Request $request)
    {
        $form = $this->createForm(ProductEditType::class, $product);
        $form->handleRequest($request);
        $product->setUpdatedOn(new \DateTime());
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash("success", "Product {$product->getName()} updated successfully!");

            return $this->redirectToRoute("product_details", ['id' => $product->getId()]);
        }

        return $this->render(":product:edit.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("product/delete/{id}", name="product_delete")
     * @return Response
     */
    public function deleteProductAction(Product $product)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        $this->addFlash("success", "Product {$product->getName()} deleted successfully!");

        return $this->redirectToRoute("homepage");
    }
}
