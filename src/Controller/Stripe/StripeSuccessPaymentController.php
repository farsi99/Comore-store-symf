<?php

namespace App\Controller\Stripe;

use App\Repository\OrderRepository;
use App\Services\CartService;
use App\Services\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeSuccessPaymentController extends AbstractController
{
    /**
     * @Route("/stripe-payment-success/{CHECKOUT_SESSION_ID}/{id}", name="stripe_payment_success")
     */
    public function index(OrderRepository $repo, EntityManagerInterface $manager, CartService $cartService, $CHECKOUT_SESSION_ID, $id): Response
    {

        $order = $repo->findOneBy(['id' => $id]);
        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $order->setStripChekoutSessionId($CHECKOUT_SESSION_ID)
            ->setIsPaid(1);
        $manager->flush();

        //On vide la panier
        $cartService->deleteCart();

        //On envoie un mail pour informer de son paiement

        return $this->render('stripe/stripe_success_payment/index.html.twig', [
            'order' => $order
        ]);
    }
}
