<?php

namespace App\Controller\Stripe;

use App\Entity\Cart;
use App\Services\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeCheckoutSessionController extends AbstractController
{
    /**
     * @Route("/create-checkout-session/{reference}", name="create_checkout_session")
     */
    public function index(?Cart $cart, OrderService $orderService, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if (!$cart) {
            return $this->redirectToRoute('home');
        }
        $order = $orderService->createOrder($cart);
        Stripe::setApiKey('sk_test_51IgtB2I4bxab2PEwaFAradi38D1xmyYYLmZcZCHlWYIwfoU8TH5peiClFyqA4hf4u0E6DqVihlHb8Yf9zhnK8rkW00XVUX3JSE');

        $checkout_session = Session::create([
            'customer_email' => $user->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' =>  $orderService->getLineItems($cart),
            'mode' => 'payment',
            'success_url' => $_ENV['YOUR_DOMAIN'] . '/stripe-payment-success/{CHECKOUT_SESSION_ID}/' . $order,
            'cancel_url' => $_ENV['YOUR_DOMAIN'] . '/stripe-payment-cancel/{CHECKOUT_SESSION_ID}/' . $order,
        ]);
        //$order->setStripChekoutSessionId($checkout_session->id);
        // echo json_encode(['id' => $checkout_session->id]);
        return $this->json(['id' => $checkout_session->id]);
    }
}
