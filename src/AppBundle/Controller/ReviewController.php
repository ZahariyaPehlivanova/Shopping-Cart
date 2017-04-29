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

            $em = $this->getDoctrine()->getManager();

            $currRating = $product->getRating();
            $product->setRating($currRating + $review->getRating());
            $em->persist($product);

            $em->persist($review);
            $em->flush();

            $this->addFlash("success", "Review added!");

            return $this->redirectToRoute("product_details", ["id" => $product->getId()]);
        }
        return $this->render("review/add.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("review/remove/{id}", name="remove_review")
     */
    public function removeReviewAction(Review $review)
    {
        if(!$this->isAuthenticated($review)){
            $this->addFlash("error", "You are not allowed to remove user's review!");
            return $this->redirectToRoute("allProducts");
        }

        $user = $this->getUser();
        $user->getReviews()->removeElement($review);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->remove($review);
        $product = $review->getProduct();
        $oldRating = $product->getRating();
        $product->setRating($oldRating - $review->getRating());

        $em->persist($product);
        $em->flush();

        $this->addFlash("success", "Review removed successfully!");

        return $this->redirectToRoute("allProducts");
    }

    private function isAuthenticated(Review $review){
        $isAdmin = $this->isGranted('ROLE_ADMIN', $this->getUser());
        $isEditor = $this->isGranted('ROLE_EDITOR', $this->getUser());
        $isAuthor = $review->getAuthor()->getId() == $this->getUser()->getId();
        return $isAdmin || $isEditor || $isAuthor;
    }
}
