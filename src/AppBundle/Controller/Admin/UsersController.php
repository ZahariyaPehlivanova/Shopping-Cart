<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Form\Admin\EditUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
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
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render(":admin/users:listAll.html.twig", [
            "users" => $users
        ]);
    }

    /**
     * @Route("admin/edit/user/{id}", name="admin_edit_user")
     * @return Response
     */
    public function editUserAction(User $user, Request $request)
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "User {$user->getEmail()} updated successfully!");

            return $this->redirectToRoute("get_all_users");
        }

        return $this->render("admin/users/adminUserEdit.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("admin/delete/user/{id}", name="admin_delete_user")
     */
    public function deleteUserAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash("success", "User {$user->getEmail()} deleted successfully!");

        return $this->redirectToRoute("get_all_users");
    }

}
