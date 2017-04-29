<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_home")
     *
     * @return Response
     */
    public function indexAction()
    {
        if(!$this->isAuthenticated()){
            $this->addFlash("error", "You are not allowed to see admin's actions!");
            return $this->redirectToRoute("allProducts");
        }
        return $this->render(":admin:home.html.twig");
    }

    private function isAuthenticated(){
        $isAdmin = $this->isGranted('ROLE_ADMIN', $this->getUser());
        $isEditor = $this->isGranted('ROLE_EDITOR', $this->getUser());
        return $isAdmin || $isEditor;
    }
}
