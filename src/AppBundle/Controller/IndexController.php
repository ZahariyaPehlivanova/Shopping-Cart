<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function sidebarMenuAction()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findAll();

        return $this->render("categories_menu.html.twig", [
            "categories" => $categories
        ]);
    }
}
