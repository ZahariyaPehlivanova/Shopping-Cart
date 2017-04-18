<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Form\Admin\AddAndEditCategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriesController extends Controller
{
    /**
     * @Route("admin/all/categories", name="get_all_categories")
     */
    public function allCategoriesAction()
    {
        $activeCategories = $this->getDoctrine()->getRepository(Category::class)->findAllActiveCategories();
        $deletedCategories = $this->getDoctrine()->getRepository(Category::class)->findAllDeletedCategories();

        return $this->render(":admin/categories:all_categories.html.twig", [
            "activeCategories" => $activeCategories,
            "deletedCategories" => $deletedCategories
        ]);
    }

    /**
     * @Route("admin/add/category", name="admin_add_category")
     * @return Response
     */
    public function addCategoryAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(AddAndEditCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Category is created successfully!');

            return $this->redirectToRoute('get_all_categories');
        }

        return $this->render(':admin/categories:admin_category_add.html.twig', [
                'category' => $category,
                'form'    => $form->createView(),
            ]
        );
    }

    /**
     * @Route("admin/edit/category/{id}", name="admin_edit_category")
     * @return Response
     */
    public function editCategoryAction(Category $category, Request $request)
    {
        $form = $this->createForm(AddAndEditCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash("success", "Category {$category->getName()} updated successfully!");

            return $this->redirectToRoute("get_all_categories");
        }

        return $this->render(":admin/categories:admin_category_edit.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("admin/restore/category/{id}", name="admin_restore_category")
     */
    public function restoreCategoryAction(Category $category)
    {
        $category->setIsDeleted(false);
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

        $this->addFlash("success", "Category {$category->getName()} restored successfully!");

        return $this->redirectToRoute("get_all_categories");
    }

    /**
     * @Route("admin/delete/category/{id}", name="admin_delete_category")
     */
    public function deleteCategoryAction(Category $category)
    {
        $category->setIsDeleted(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

        $this->addFlash("success", "Category {$category->getName()} deleted successfully!");

        return $this->redirectToRoute("get_all_categories");
    }
}
