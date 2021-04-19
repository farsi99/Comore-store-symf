<?php

namespace App\Controller\Stripe;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeCancelPaymentController extends AbstractController
{
    /**
     * @Route("/stripe-payment-cancel/{CHECKOUT_SESSION_ID}/{id}", name="stripe_payment_cancel")
     */
    public function index(OrderRepository $repo, EntityManagerInterface $manager, $CHECKOUT_SESSION_ID, $id): Response
    {

        $order = $repo->findOneBy(['id' => $id]);
        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $order->setStripChekoutSessionId($CHECKOUT_SESSION_ID)
            ->setIsPaid(false)
            ->setState(0);
        $manager->flush();

        return $this->render('stripe/stripe_cancel_payment/index.html.twig', [
            'order' => $order,
        ]);
    }
}
