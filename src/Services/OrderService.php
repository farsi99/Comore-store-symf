<?php

namespace App\Services;

use App\Entity\Cart;
use App\Entity\CartDetails;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Cast\Double;

class OrderService
{

    private $manager;
    private $repoProduct;

    public function __construct(EntityManagerInterface $manager, ProductRepository $repoProduct)
    {
        $this->manager = $manager;
        $this->repoProduct = $repoProduct;
    }

    public function  createOrder($cart)
    {
        $order = new Order();
        $order->setReference($cart->getReference())
            ->setFullName($cart->getFullName())
            ->setCarrierName($cart->getCarrierName())
            ->setCarrierPrice($cart->getCarrierPrice() / 100)
            ->setDeliveryAddress($cart->getDeliveryAddress())
            ->setMoreInformation($cart->getMoreInformation())
            ->setCreatedAt($cart->getCreatedAt())
            ->setQuantity($cart->getQuantity())
            ->setSubtotalht($cart->getSubtotalht() / 100)
            ->setSubtotalttc($cart->getSubtotalttc() / 100)
            ->setTaxe($cart->getTaxe() / 100)
            ->setUser($cart->getUser());
        $this->manager->persist($order);

        $products = $cart->getCartDetails()->getValues();
        foreach ($products as $cart_product) {
            $orderDetail = new OrderDetails();

            $orderDetail->setOrders($order)
                ->setProductName($cart_product->getProductName())
                ->setProductPrice($cart_product->getProductPrice())
                ->setProductQuantity($cart_product->getProductQuantity())
                ->setSubTotalttc($cart_product->getSubTotalttc())
                ->setSubTotalht($cart_product->getSubTotalht())
                ->setTaxe($cart_product->getTaxe());

            $this->manager->persist($orderDetail);
        }
        $this->manager->flush();

        return $order->getId();
    }


    public function saveCart($data, $user)
    {
        $cart = new Cart();
        $address = $data['checkout']['address'];
        $carrier = $data['checkout']['carrier'];
        $information = $data['checkout']['informations'];
        $reference = $this->generateUuid();

        $cart->setReference($reference)
            ->setCarrierName($carrier->getName())
            ->setCarrierPrice($carrier->getPrice() / 100)
            ->setFullName($address->getFullName())
            ->setDeliveryAddress($address)
            ->setMoreInformation($information)
            ->setQuantity($data['data']['quantity_car'])
            ->setSubtotalht($data['data']['subtotalht'])
            ->setTaxe($data['data']['taxe'])
            ->setSubtotalttc(round(($data['data']['subtotalttc'] + $carrier->getPrice() / 100), 2))
            ->setUser($user)
            ->setCreatedAt(new \DateTime());

        $this->manager->persist($cart);

        $cart_details_array = [];

        //Detail d'un panier
        foreach ($data['products'] as $products) {
            $cartDetail = new CartDetails();
            $subtotal = $products['quantity'] * $products['product']->getPrice() / 100;

            $cartDetail->setCarts($cart)
                ->setProductName($products['product']->getName())
                ->setProductPrice($products['product']->getPrice() / 100)
                ->setProductQuantity($products['quantity'])
                ->setSubTotalht($subtotal)
                ->setSubTotalttc($subtotal * 1.2)
                ->setTaxe($subtotal * 0.2);

            $this->manager->persist($cartDetail);

            $cart_details_array[] = $cartDetail;
        }

        $this->manager->flush();

        return $reference;
    }

    public function getLineItems($cart)
    {
        $cartDetail = $cart->getCartDetails();
        $line_items = [];
        foreach ($cartDetail as $details) {
            $product = $this->repoProduct->findOneByName($details->getProductName());
            $line_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getName(),
                        'images' => [$_ENV['YOUR_DOMAIN'] . '/upload/products/' . $product->getImage()],
                    ],
                ],
                'quantity' => $details->getProductQuantity(),
            ];
        }

        //Transporteur
        $line_items[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $cart->getCarrierPrice(),
                'product_data' => [
                    'name' => 'Livraison(' . $cart->getCarrierName() . ')',
                    'images' => [$_ENV['YOUR_DOMAIN'] . '/upload/products/'],
                ],
            ],
            'quantity' => 1,
        ];

        //Taxe
        $line_items[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $cart->getTaxe(),
                'product_data' => [
                    'name' => 'TVA (20%)',
                    'images' => [$_ENV['YOUR_DOMAIN'] . '/upload/products/'],
                ],
            ],
            'quantity' => 1,
        ];



        return $line_items;
    }

    /**
     * Cette méthode nous genere un identifiant unique pour la reference d'un produit
     */
    public function generateUuid()
    {
        //Initialise le gereateur de nombre aléatoire
        mt_srand((float) microtime() * 1000000);
        //renvoie une chaine en majiscule
        $charid = strtoupper(md5(uniqid(rand(), true)));
        //Generer une chaine à partir d'un nombre
        $hyphen = chr(45);

        //retourne un segement de chaine
        $uuid = ""
            . substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);
        return $uuid;
    }
}
