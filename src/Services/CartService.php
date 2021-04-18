<?php

namespace App\Services;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{

    private $session;
    private $repoProduct;
    private $tva = 0.2;

    public function __construct(SessionInterface $session, ProductRepository $repoProduct)
    {
        $this->session = $session;
        $this->repoProduct = $repoProduct;
    }

    /**
     * Ajouter un produit au panier
     */
    public function addToCart($idProduct)
    {
        $cart = $this->session->get('cart');
        if (isset($cart[$idProduct])) {
            //produit deja dans le panier
            $cart[$idProduct]++;
        } else {
            //le produit n'est pas encore dans le panier
            $cart[$idProduct] = 1;
        }

        $this->updateCart($cart);
    }

    /**
     * Supprimer un produit du panier
     */
    public function deleteFromCart($idProduct)
    {
        $cart = $this->session->get('cart');
        if (isset($cart[$idProduct])) {
            //produit dans le panier avec plusieurs element
            if ($cart[$idProduct] > 1) {
                $cart[$idProduct]--;
            } else {
                unset($cart[$idProduct]);
            }
            $this->updateCart($cart);
        }
    }

    /**
     * Cette méthode supprimer tous les produits ayant plusieurs quantités du panier
     */
    public function deleteAllCart($idProduct)
    {
        $cart = $this->session->get('cart');
        if (isset($cart[$idProduct])) {
            unset($cart[$idProduct]);
            $this->updateCart($cart);
        }
    }

    /**
     * Supprimer tous les produits du panier
     */
    public function deleteCart()
    {
        $this->updateCart([]);
    }

    /**
     * Mise à jour du panier
     */
    public function updateCart($cart)
    {
        $this->session->set('cart', $cart);
        $this->session->set('cartData', $this->getFullCart());
    }

    /**
     * cette méthode nous permet de recuperer notre panier
     */
    public function getCart()
    {
        return $this->session->get('cart', []);
    }

    /**
     * cette méthode recupere tous les produits qui sont dans le panier
     */
    public function getFullCart()
    {
        $cart = $this->session->get('cart');
        $fullCart = [];
        $quantity_cart = 0;
        $subtotal = 0;
        if ($cart) {
            foreach ($cart as $id => $quantity) {
                $product = $this->repoProduct->find($id);
                if ($product) {
                    // produit recupere

                    //on verifie si la quantité commandé est inferieur au stock
                    if ($quantity > $product->getQuantity()) {
                        $quantity = $product->getQuantity();
                        $cart[$id] = $quantity;
                        $this->updateCart($cart);
                    }
                    $fullCart["products"][] = [
                        'quantity' => $quantity,
                        'product' => $product
                    ];
                    $quantity_cart += $quantity;
                    $subtotal += $quantity * $product->getPrice() / 100;
                } else {
                    //id  incorrecte
                    $this->deleteFromCart($id);
                }
            }
            $fullCart['data'] = [
                'quantity_car' => $quantity_cart,
                'subtotalht' => $subtotal,
                'taxe' => round($subtotal * $this->tva, 2),
                'subtotalttc' => round(($subtotal + ($subtotal * $this->tva)), 2)
            ];
            return $fullCart;
        }
    }
}
