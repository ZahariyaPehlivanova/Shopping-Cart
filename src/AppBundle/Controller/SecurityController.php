<?php

namespace AppBundle\Controller;

use AppBundle\Form\UserLoginType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction(){
        if($this->getUser()){
            $this->addFlash("error", "You are already logged in!");
            return $this->redirectToRoute("allProducts");
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(UserLoginType::class, [
            '_username' => $lastUsername
        ]);

        return $this->render(
            'security/login.html.twig',
            array(
                'form' => $form->createView(),
                'error' => $error,
            )
        );
    }

    /**
     * @Route("/logout", name="security_logout")
     * @Security("is_authenticated()")
     */
    public function logoutAction(){

    }
}
