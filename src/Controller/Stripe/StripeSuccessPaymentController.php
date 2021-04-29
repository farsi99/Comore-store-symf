<?php

namespace App\Controller\Stripe;

use App\Entity\EmailModel;
use App\Repository\OrderRepository;
use App\Services\CartService;
use App\Services\EmailSender;
use App\Services\OrderService;
use App\Services\StockService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeSuccessPaymentController extends AbstractController
{
    /**
     * @Route("/stripe-payment-success/{CHECKOUT_SESSION_ID}/{id}", name="stripe_payment_success")
     */
    public function index(OrderRepository $repo, EntityManagerInterface $manager, CartService $cartService, StockService $stock, EmailSender $emailsender,  $CHECKOUT_SESSION_ID, $id): Response
    {

        $order = $repo->findOneBy(['id' => $id]);
        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        //destockage
        $stock->destock($order, $this->getUser());

        $order->setStripChekoutSessionId($CHECKOUT_SESSION_ID)
            ->setIsPaid(1)
            ->setState(1);
        $manager->flush();

        //On vide la panier
        $cartService->deleteCart();
        $orderDetails = $order->getOrderDetails()->getValues();
        //On envoie un mail pour informer de son paiement
        $user = $this->getUser();
        $emailMode = new EmailModel();
        $emailMode->setTitle('Confirmation de paiement de votre commande');
        $body = "<em>Reference:
						" . $order->getReference() . "</em><br>
					<em>Date de la commande:
						 </em><br><br>				
						<table class='table'>
							<thead>
								<tr>
									<th style='widht:30%'>Produit</th>
									<th style='widht:15%'>Prix</th>
									<th style='widht:15%'>Quantité</th>
									<th style='widht:10%'>Total HT</th>
									<th style='widht:10%'>TVA(20%)</th>
									<th style='widht:10%'>Total TTC</th>
								</tr>
							</thead>
							<tbody>";
        if ($orderDetails) {
            foreach ($orderDetails as $detail) {
                $body .= "<tr>
                                <td style='widht:30%'>
                                    <a href='#'>" . $detail->getProductName() . "</a>
                                </td>
                                <td style='widht:15%'>" . $detail->getProductPrice() . "
                                    €</td>
                                <td style='widht:15%'>" . $detail->getProductQuantity() . "
                                </td style='widht:10%'>
                                <td>" . $detail->getSubTotalht() . "
                                    €</td>
                                <td style='widht:10%'>" . $detail->getTaxe() . "
                                    €</td>
                                <td style='widht:10%'>" . $detail->getSubTotalttc() . "
                                    €</td>

                            </tr>";
            }
        }
        $body .= "</tbody>
						</table>
		<hr>
						<table>
							<tbody>
								<tr>
									<td style='widht:30%'>sous-total HT</td>
									<td>" . ($order->getSubtotalht() / 100) . "
										€</td>
								</tr>
								<tr>
									<td style='widht:30%'>TVA (20%)</td>
									<td>" . ($order->getTaxe() / 100) . "
										€</td>
								</tr>
								<tr>
									<td style='widht:30%'>Livraison (" . $order->getCarrierName() . ")</td>
									<td>" . ($order->getCarrierPrice() / 100) . "
										€</td>
								</tr>
								<tr>
									<td style='widht:30%'>Total TTC</td>
									<td>
										<strong>" . ($order->getSubtotalttc() / 100) . "</strong>
									</td>
								</tr>
							</tbody>
						</table>
				";

        $emailMode->setContent($body);
        $emailsender->sendEmailByMailJet($user, $emailMode);
        return $this->render('stripe/stripe_success_payment/index.html.twig', [
            'order' => $order
        ]);
    }
}
