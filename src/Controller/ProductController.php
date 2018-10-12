<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductPhoto;
use App\Form\ProductPhotoType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends Controller
{
    /**
     * @Route("/", name="product_index", methods="GET")
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', ['products' => $productRepository->findAll()]);
    }

    /**
     * @Route("/new", name="product_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $this->denyAccessUnlessGranted('create', $product);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entity = $form->getData();
            $files = $form->get('photos')->getData();
            foreach ($files as $file ) {
                $filename = md5(uniqid()) . "." . $file->getClientOriginalName();
                $file->move($this->getParameter('photos_directory'), $filename);
                $photo = new ProductPhoto();
                $photo = $photo->setName($filename);
                $product->addPhoto($photo);
            }

            $em = $this->getDoctrine()->getManager();
            $product->setDateOfCreation(new \DateTime('now'));
            $product->setDateOfLastModification(new \DateTime('now'));
            $em->persist($product);
            $em->flush();
            if (null != $product->getId()) {
                $this->addFlash('success', 'Product added successfuly');
            } else {
                $this->addFlash('error', 'Product not added to database');
            }

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods="GET")
     */
    public function show(Product $product): Response
    {
        $this->denyAccessUnlessGranted('view', $product);
        return $this->render('product/show.html.twig', ['product' => $product]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods="GET|POST|DELETE")
     */
    public function edit(Request $request, Product $product): Response
    {
        $this->denyAccessUnlessGranted('edit', $product);
        if (!empty($request->request->get('itemid'))) {
            $itemsId = $request->request->get('itemid');
            $em = $this->getDoctrine()->getManager();
            $photos = $em->getRepository(ProductPhoto::class)->findProductPhotos($product);
            foreach ($itemsId as $order => $item) {
                foreach ($photos as $photo) {
                    if ($photo->getId() == $item) {
                        $photo->setPosition($order);
                    }
                }
            }
            $em->flush();
        }

        if (!empty($request->request->get('photoId'))) {
            $photoId = $request->request->get('photoId');
            if (!empty($photoId)) {
                $em = $this->getDoctrine()->getManager();
                $photos = $em->getRepository(ProductPhoto::class)->findProductPhotos($product);
                foreach ($photos as $photo) {
                    if ($photo->getId() == $photoId) {
                        $photo->setIsMain(true);
                    } else {
                        $photo->setIsMain(false);
                    }
                }
                $em->flush();
            }
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form->get('photos')->getData();
            foreach ($files as $file) {
                $filename = md5(uniqid()) . "." . $file->getClientOriginalName();
                $file->move($this->getParameter('photos_directory'), $filename);
                $photo = new ProductPhoto();
                $photo->setName($filename);
                $product->addPhoto($photo);
            }

            $product->setDateOfLastModification(new \DateTime('now'));
            $this->getDoctrine()->getManager()->flush();
            if (null != $product->getId()) {
                $this->addFlash('success', 'Product updated successfuly');
            } else {
                $this->addFlash('error', 'Product not updated to database');
            }

            return $this->redirectToRoute('product_edit', ['id' => $product->getId()]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods="DELETE")
     */
    public function delete(Request $request, Product $product): Response
    {
        $this->denyAccessUnlessGranted('delete', $product);
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($product);
            $em->flush();
            if (null != $product->getId()) {
                $this->addFlash('error', 'Product not deleted');
            } else {
                $this->addFlash('success', 'Product deleted successfuly');
            }
        }

        return $this->redirectToRoute('product_index');
    }
}
