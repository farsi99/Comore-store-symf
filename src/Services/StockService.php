<?php

namespace App\Services;

use App\Entity\Order;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class StockService
{

    private $manager;
    private $productRepo;
    private $cartRepo;

    public function __construct(EntityManagerInterface $manager, ProductRepository $productRepo, CartRepository $cartRepo)
    {
        $this->manager = $manager;
        $this->productRepo = $productRepo;
        $this->cartRepo = $cartRepo;
    }

    /**
     * Cette méthode nous permet de destocker les porduits
     */
    public function destock(Order $order, $user)
    {

        //$orderDetails = $this->detailRepo->findByOrders($order);
        $orderDetails = $order->getOrderDetails()->getValues();
        foreach ($orderDetails as  $detail) {
            $product = $this->productRepo->findByName($detail->getProductName())[0];
            $newQuantity = $product->getQuantity() - $detail->getProductQuantity();
            $product->setQuantity($newQuantity);
            $this->manager->flush();
        }
        //suppression du produit dans le panier
        $cart = $this->cartRepo->findBy(['reference' => $order->getReference(), 'user' => $user])[0];
        $cart_details = $cart->getCartDetails()->getValues();
        foreach ($cart_details as $cart_detail) {
            $this->manager->remove($cart_detail);
        }
        $this->manager->remove($cart);
        $this->manager->flush();
    }
}
