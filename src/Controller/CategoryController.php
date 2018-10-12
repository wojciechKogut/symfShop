<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/", name="category_index", methods="GET")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', ['categories' => $categoryRepository->findAll()]);
    }

    /**
     * @Route("/new", name="category_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $this->denyAccessUnlessGranted('create', $category);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $category->setDateOfCreation(new \DateTime('now'));
            $category->setDateOfLastModification(new \DateTime('now'));
            $em->persist($category);
            $em->flush();
            if (null != $category->getId()) {
                $this->addFlash('success', 'Category added successfuly');
            } else {
                $this->addFlash('error', 'Category not added to database');
            }

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_show", methods="GET")
     */
    public function show(Category $category): Response
    {
        $this->denyAccessUnlessGranted('view', $category);
        return $this->render('category/show.html.twig', ['category' => $category]);
    }

    /**
     * @Route("/{id}/edit", name="category_edit", methods="GET|POST")
     */
    public function edit(Request $request, Category $category): Response
    {
        $this->denyAccessUnlessGranted('edit', $category);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setDateOfLastModification(new \DateTime('now'));
            $this->getDoctrine()->getManager()->flush();
            if (null != $category->getId()) {
                $this->addFlash('success', 'Category updated successfuly');
            } else {
                $this->addFlash('error', 'Category not updated to database');
            }

            return $this->redirectToRoute('category_edit', ['id' => $category->getId()]);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_delete", methods="DELETE")
     */
    public function delete(Request $request, Category $category): Response
    {
        $this->denyAccessUnlessGranted('delete', $category);
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
            if (null != $category->getId()) {
                $this->addFlash('error', 'Category not deleted');
            }  else {
                $this->addFlash('success', 'Category deleted successfuly');
            }
        }

        return $this->redirectToRoute('category_index');
    }

    public function renderCategories(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('includes/menu.html.twig', ['categories' => $categories]);
    }
}
