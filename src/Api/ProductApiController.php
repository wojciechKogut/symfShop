<?php

namespace App\Api;

use App\Entity\Product;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductApiController extends FOSRestController
{
    /**
     * @Rest\Get("/api/products")
     * @return \FOS\RestBundle\View\View|Response
     */
    public function getProductsAction()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->selectProductsIdsNamesAndPrices();
        if (null == $products) {
            $view = $this->view('No products', 404);
        } else {
            $view = $this->view($products, 200);
        }
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/api/product/{id}")
     * @param int $id
     * @return \FOS\RestBundle\View\View|Response
     */
    public function getProductAction(int $id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if (null == $product) {
            $view = $this->view('No product found', 404);
        } else {
            $view = $this->view($product, 200);
        }

        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/api/new/product/")
     * @param Request $request
     * @return \FOS\RestBundle\View\View|Response
     */
    public function newProductAction(Request $request)
    {
        $err = [];
        $product = new Product();
        $productName = $request->request->get('name');
        $productPrice = $request->request->get('price');
        $productDescription = $request->request->get('description');

        if (empty($productName)) $err[]  = 'Product name field is required';
        if (empty($productPrice)) $err[] = 'Product price field is required';

        if (!empty($err)) {
            $view = $this->view($err, 404);
        } else {
            $product->setName($productName);
            $product->setDescription($productDescription);
            $product->setPrice($productPrice);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
        }

        if (null == $product->getId()) {
            $err[] = 'Product not added to database';
            $view = $this->view($err, 404);
        } else {
            $view = $this->view('Product added successfully', 200);
        }

        return $this->handleView($view);
    }

    /**
     * @Rest\Put("/api/edit/product/{id}")
     * @param Request $request
     * @param int $id
     * @return \FOS\RestBundle\View\View|Response
     */
    public function updateProductAction(Request $request, int $id)
    {
        $err = [];
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $productName = $request->request->get('name');
        $productPrice = $request->request->get('price');
        $productDescription = $request->request->get('description');

        if (empty($productName)) $err[]  = 'Product name field is required';
        if (empty($productPrice)) $err[] = 'Product price field is required';

        if (!empty($err)) {
            $view = $this->view($err, 404);
        } else {
            $product->setName($productName);
            $product->setPrice($productPrice);
            $product->setDescription($productDescription);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $view = $this->view('Product updated successfully', 200);
        }

        return $this->handleView($view);

    }

    /**
     * @Rest\Delete("/api/delete/product/{id}")
     * @param int $id
     * @return \FOS\RestBundle\View\View|Response
     */
    public function deleteProductAction(int $id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if (null == $product) {
            return $view = $this->view('No product found', 404);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();
        $view = $this->view('Product deleted successfully', 200);

        return $this->handleView($view);
    }
}
