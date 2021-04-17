<?php

namespace App\Controller\Cart;

use App\Form\ChekoutType;
use App\Services\CartService;
use App\Services\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    private $cartService;
    private $session;
    public function __construct(cartService $cartService, SessionInterface $session)
    {
        $this->cartService = $cartService;
        $this->session = $session;
    }
    /**
     * @Route("/commande", name="checkout")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $cart = $this->cartService->getFullCart();
        if (!isset($cart['products'])) {
            return $this->redirectToRoute("home");
        }
        if (!$user->getAddresses()->getValues()) {
            $this->addFlash('checkout_message', 'Merci de créer une adresse avant de continuer votre commande!');
            return $this->redirectToRoute('address_new');
        }

        if ($this->session->get('checkoutData')) {
            return $this->redirectToRoute('checkout_confirm');
        }

        $form = $this->createForm(ChekoutType::class, null, ['user' => $user]);

        return $this->render('checkout/index.html.twig', [
            'cart' => $cart,
            'checkout' => $form->createView()
        ]);
    }


    /**
     * @Route("/commande/confirm", name="checkout_confirm")
     */
    public function confirm(Request $request, OrderService $orderService)
    {
        $user = $this->getUser();
        $cart = $this->cartService->getFullCart();

        if (!isset($cart['products'])) {
            return $this->redirectToRoute("home");
        }
        if (!$user->getAddresses()->getValues()) {
            $this->addFlash('checkout_message', 'Merci de créer une adresse avant de continuer votre commande!');
            return $this->redirectToRoute('address_new');
        }

        $form = $this->createForm(ChekoutType::class, null, ['user' => $user]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() || $this->session->get('checkoutData')) {
            if ($this->session->get('checkoutData')) {
                $data = $this->session->get('checkoutData');
            } else {
                $data = $form->getData();
                $this->session->set('checkoutData', $data);
            }
            $address = $data['address'];
            $carrier = $data['carrier'];
            $informations = $data['informations'];
            //Save cart
            $cart['checkout'] = $data;
            $reference = $orderService->saveCart($cart, $user);

            return $this->render('checkout/confirm.html.twig', [
                'address' => $address,
                'carrier' => $carrier,
                'informations' => $informations,
                'cart' => $cart,
                'reference' => $reference,
                'checkout' => $form->createView()
            ]);
        }

        return $this->redirectToRoute('checkout');
    }

    /**
     * @Route("commade/editer",name="checkout_edit")
     */
    public function checkoutEdit(): Response
    {
        $this->session->set('checkoutData', []);
        return $this->redirectToRoute("checkout");
    }
}
