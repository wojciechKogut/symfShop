<?php

namespace App\Controller;

use App\Entity\ProductPhoto;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product/photo")
 */
class ProductPhotoController extends Controller
{
    /**
     * @Route("/delete/{id}/{token}", name="product_photo_delete", methods="GET|DELETE")
     */
    public function delete(Request $request, ProductPhoto $productPhoto, $token): Response
    {
        $productId = $request->query->get('productId');
        if ($this->isCsrfTokenValid('delete'.$productPhoto->getId(), $token)) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productPhoto);
            $em->flush();
        }

        return $this->redirectToRoute('product_edit', ['id' => $productId]);
    }
}
