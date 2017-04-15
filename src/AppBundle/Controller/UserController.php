<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserEditType;
use AppBundle\Form\UserRegistrationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register_form")
     * @Method("GET")
     * @Security("is_anonymous()")
     * @return Response
     */
    public function registerAction()
    {
        $form = $this->createForm(UserRegistrationType::class);
        return $this->render('user/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/register", name="user_register_process")
     * @Method("POST")
     * @return Response
     */
    public function registerProcessAction(Request $request)
    {
        $user = new User();
        $em = $this->getDoctrine()->getManager();

        $userRole = $em->getRepository(Role::class)
            ->findOneBy(["name" => "ROLE_USER"]);
        $user->addRole($userRole);

        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Welcome '.$user->getEmail());
            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }

        return $this->render('user/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/profile", name="user_profile")
     * @Security(expression="is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function userProfileAction()
    {
        $user = $this->getUser();
        return $this->render(":user:profile.html.twig", [
            "user" => $user
        ]);
    }

    /**
     * @Route("/profile/edit", name="profile_edit_process")
     * @Security(expression="is_granted('IS_AUTHENTICATED_FULLY')")
     * @Method("GET")
     */
    public function userEditAction()
    {
        $user = $this->getUser();
        $form = $this->createForm(UserEditType::class, $user);
        return $this->render('user/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/profile/edit", name="profile_edit")
     * @Security(expression="is_granted('IS_AUTHENTICATED_FULLY')")
     * @Method("POST")
     */
    public function userEditProcessAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserEditType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "Profile updated!");

            return $this->redirectToRoute("user_profile");
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView()]);
    }
}
