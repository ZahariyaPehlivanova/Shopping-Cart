<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Product;
use AppBundle\Entity\Review;
use AppBundle\Entity\User;
use AppBundle\Form\Admin\EditUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class UsersController extends Controller
{
    /**
     * @Route("admin/all/users", name="get_all_users")
     */
    public function allUsersAction()
    {
        if(!$this->isGranted('ROLE_ADMIN', $this->getUser())){
            $this->addFlash("error", "You are not allowed to see the users!");
            return $this->redirectToRoute("allProducts");
        }
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render(":admin/users:all_users.html.twig", [
            "users" => $users
        ]);
    }

    /**
     * @Route("admin/edit/user/{id}", name="admin_edit_user")
     * @return Response
     */
    public function editUserAction(User $user, Request $request)
    {
        if(!$this->isGranted('ROLE_ADMIN', $this->getUser())){
            $this->addFlash("error", "You are not allowed to edit this user!");
            return $this->redirectToRoute("allProducts");
        }
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "User {$user->getUsername()} updated successfully!");

            return $this->redirectToRoute("get_all_users");
        }

        return $this->render(":admin/users:admin_user_edit.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("admin/delete/user/{id}", name="admin_delete_user")
     */
    public function deleteUserAction(User $user)
    {
        if(!$this->isGranted('ROLE_ADMIN', $this->getUser())){
            $this->addFlash("error", "You are not allowed to delete this user!");
            return $this->redirectToRoute("allProducts");
        }
        if($user->getUsername() === "admin"){
            $this->addFlash("error", "You are cannot delete the admin!");
            return $this->redirectToRoute("get_all_users");
        }

        $reviews = $user->getReviews();
        foreach ($reviews as $review){
            $this->removeUserReviewAction($review, $user);
        }
        $em = $this->getDoctrine()->getManager();
        $products = $this->getDoctrine()->getRepository(Product::class)->findAllProductsByAuthor($user->getUsername());
        foreach ($products as $product){
            foreach ($product->getReviews() as $review){
                $em->remove($review);
            }
            $em->flush();
            $em->remove($product);
            $em->flush();
        }
        $em->remove($user);
        $em->flush();

        $this->addFlash("success", "User {$user->getEmail()} deleted successfully!");

        return $this->redirectToRoute("get_all_users");
    }

    /**
     * @Route("admin/user/reviews/{id}", name="get_user_reviews")
     */
    public function userReviewsAction(User $user)
    {
        if(!$this->isGranted('ROLE_ADMIN', $this->getUser())){
            $this->addFlash("error", "You are not allowed to see the reviews of the users!");
            return $this->redirectToRoute("allProducts");
        }

        $reviews = $user->getReviews();

        return $this->render(":admin/users:user_reviews.html.twig", [
            "reviews" => $reviews,
            "user" => $user
        ]);
    }

    /**
     * @Route("admin/user/removeReview/{id}/{user}", name="remove_user_review")
     */
    public function removeUserReviewAction(Review $review, User $user)
    {
        if(!$this->isGranted('ROLE_ADMIN', $this->getUser())){
            $this->addFlash("error", "You are not allowed to remove user's review!");
            return $this->redirectToRoute("allProducts");
        }

        $user->getReviews()->removeElement($review);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->remove($review);
        $product = $review->getProduct();
        $oldRating = $product->getRating();
        $product->setRating($oldRating - $review->getRating());

        $em->persist($product);
        $em->flush();

        $this->addFlash("success", "User review removed successfully!");

        return $this->render(":admin/users:user_reviews.html.twig", [
            "reviews" => $user->getReviews(),
            "user" => $user
        ]);
    }

    /**
     * @Route("admin/user/possessions/{id}", name="get_user_possessions")
     */
    public function userPossessionsAction(User $user)
    {
        if(!$this->isGranted('ROLE_ADMIN', $this->getUser())){
            $this->addFlash("error", "You are not allowed to see the possessions of the users!");
            return $this->redirectToRoute("allProducts");
        }

        $products = $user->getProducts();

        return $this->render(":admin/users:user_possessions.html.twig", [
            "products" => $products,
            "user" => $user
        ]);
    }

    /**
     * @Route("admin/user/removePossession/{id}/{user}", name="remove_user_possession")
     */
    public function removeUserPossessionAction(Product $product,User $user)
    {
        if(!$this->isGranted('ROLE_ADMIN', $this->getUser())){
            $this->addFlash("error", "You are not allowed to remove user's possession!");
            return $this->redirectToRoute("allProducts");
        }
        $user->getProducts()->removeElement($product);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash("success", "User possession removed successfully!");

        return $this->render(":admin/users:user_possessions.html.twig", [
            "products" => $user->getProducts()
        ]);
    }
}
