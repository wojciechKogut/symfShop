<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class ShoppingCartController
 * @package App\Controller
 * @Route("/cart")
 */
class ShoppingCartController extends Controller
{
    /**
     * @Route("/product/{id}", name="shopping_cart_new_product")
     * @param int $id
     * @return string
     */
    public function new(int $id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if (!empty($this->get('session')->get('cartProducts'))) {
            $cartProducts = $this->get('session')->get('cartProducts');
            $cartProducts[] = $product;
            $this->get('session')->set('cartProducts',$cartProducts);
        } else {
            $cartProducts[] = $product;
            $this->get('session')->set('cartProducts',$cartProducts);
        }

        $productData = ['name' => $product->getName(), 'price' => $product->getPrice(), 'image' => $product->getMainImage(), 'counter' => count($this->get('session')->get('cartProducts')) ];
        return json_encode($productData);
    }

    /**
     * @Route("/", methods="GET", name="shopping_cart_show_products")
     * @return Response
     */
    public function show():Response
    {
        $products = [];
        $totalPrice = 0;
        $cartProducts = $this->get('session')->get('cartProducts');
        $productsIds = [];

        if (!empty($cartProducts)) {
            foreach ($cartProducts as $index => $product) {
                $totalPrice += $product->getPrice();
                $productsIds[$index] = $product->getId();
            }

            //quantity of each products
            $products['quantity'] = array_count_values($productsIds);
            $uniqueProductsIds = array_unique($productsIds);
            foreach ($cartProducts as $index => $product) {
                if (array_key_exists($index, $uniqueProductsIds)) {
                    $products['cart_products'][$product->getId()] = $product;
                }
            }
            $products['total_quantity'] = count($cartProducts);
            $products['total_price'] = $totalPrice;
        }

        return $this->render('cart/shopping_cart.html.twig', ['products' => $products]);
    }

    /**
     * @Route("/product/delete/{id}", name="shopping_cart_delete_product", methods="DELETE")
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $products = $this->get('session')->get('cartProducts');

        foreach ($products as $index => $product) {
            if ($product->getId() == $id) {
                unset($products[$index]);
            }
        }

        $this->get('session')->set('cartProducts', $products);

        return $this->redirectToRoute('shopping_cart_show_products');
    }
}