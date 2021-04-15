<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $repo): Response
    {
        $products = $repo->findAll();
        $productbestSaller = $repo->findByIsBesteSeller(1);
        $productIsFeatured = $repo->findByIsFeatured(1);
        $productisSepecialOffer = $repo->findByIsSepecialOffer(1);
        $productisNewArrival = $repo->findByIsNewArrival(1);

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'productbestSaller' => $productbestSaller,
            'productIsFeatured' => $productIsFeatured,
            'productisSepecialOffer' => $productisSepecialOffer,
            'productisNewArrival' => $productisNewArrival


        ]);
    }

    /**
     * Nous affiche les détail d'un produits
     * @Route("/produit/{slug}", name="product_detail")
     */
    public function show(?Product $product): Response
    {
        if (!$product) {
            return $this->redirectToRoute('home');
        }
        return $this->render("home/single_product.html.twig", [
            'produit' => $product
        ]);
    }
}
