<?php

namespace App\Controller;

use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    /**
     * @Route("/panier", name="cart")
     */
    public function index(): Response
    {
        $cart = $this->cartService->getFullCart();
        if (!isset($cart['products'])) {
            return $this->redirectToRoute("home");
        }
        return $this->render('cart/index.html.twig', [
            'cart' => $cart
        ]);
    }

    /**
     * @Route("/panier/ajout/{id}", name="add_cart")
     */
    public function addToCart($id): Response
    {
        $this->cartService->addToCart($id);
        return $this->redirectToRoute("cart");
    }

    /**
     * @Route("/panier/supprimer/{id}", name="delete_cart")
     */
    public function deleteToCart($id): Response
    {
        $this->cartService->deleteFromCart($id);
        return $this->redirectToRoute("cart");
    }

    /**
     * @Route("/panier/supp-tous/{id}", name="deleteAll_cart")
     */
    public function deleteAllProduct($id)
    {
        $this->cartService->deleteAllCart($id);
        return $this->redirectToRoute("cart");
    }
}
