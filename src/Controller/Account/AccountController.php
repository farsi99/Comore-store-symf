<?php

namespace App\Controller\Account;

use App\Entity\Order;
use App\Repository\OrderDetailsRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account")
 */
class AccountController extends AbstractController
{

    /**
     * @Route("/", name="account")
     */
    public function index(OrderRepository $repoOrder): Response
    {
        $user = $this->getUser();
        $orders = $repoOrder->findBy(['isPaid' => true, 'user' => $user], ['id' => 'DESC']);

        return $this->render('account/index.html.twig', [
            'title' => 'Mon compte',
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/detail-commande/{id}", name="show_order")
     */
    public function showOrder(?Order $order, OrderDetailsRepository $repoDetail)
    {
        $order = $order;
        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $details = $repoDetail->findBy(['orders' => $order]);

        return $this->render('account/detail_order.html.twig', [
            'title' => 'Mon compte',
            'order' => $order,
            'details' => $details
        ]);
    }
}
