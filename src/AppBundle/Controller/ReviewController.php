<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Review;
use AppBundle\Form\AddReviewType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ReviewController extends Controller
{
    /**
     * @Route("review/add/{id}", name="add_review")
     *
     * @param Product $product
     * @param Request $request
     * @return Response
     * @Security(expression="is_granted('IS_AUTHENTICATED_FULLY')")
     *
     */
    public function addReviewProcessAction(Request $request, Product $product)
    {
        $review = new Review();
        $form = $this->createForm(AddReviewType::class, $review);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $review->setAddedOn(new \DateTime());
            $review->setAuthor($this->getUser());
            $review->setProduct($product);

            $currRating = $product->getRating();
            $product->setRating($currRating + $review->getRating());

            $em = $this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();

            $this->addFlash("success", "Review added!");

            return $this->redirectToRoute("product_details", ["id" => $product->getId()]);
        }
        return $this->render("review/add.html.twig", [
            "form" => $form->createView()
        ]);
    }
}
