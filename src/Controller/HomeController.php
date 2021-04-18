<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SearchProduct;
use App\Form\SearchProductType;
use App\Repository\ProductRepository;
use App\Repository\HomeSliderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $repo, HomeSliderRepository $repoSlider): Response
    {
        $products = $repo->findAll();
        $productbestSaller = $repo->findByIsBesteSeller(1);
        $productIsFeatured = $repo->findByIsFeatured(1);
        $productisSepecialOffer = $repo->findByIsSepecialOffer(1);
        $productisNewArrival = $repo->findByIsNewArrival(1);
        $homeslider = $repoSlider->findBy(['isDisplayed' => true]);
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'productbestSaller' => $productbestSaller,
            'productIsFeatured' => $productIsFeatured,
            'productisSepecialOffer' => $productisSepecialOffer,
            'productisNewArrival' => $productisNewArrival,
            'homeslider' => $homeslider
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

    /**
     * @Route("/boutique", name="shop")
     */
    public function shop(ProductRepository $repo, Request $request): Response
    {
        $products = $repo->findAll();
        $search = new SearchProduct();
        $form = $this->createForm(SearchProductType::class, $search);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $products = $repo->findWithSearch($search);
        }

        return $this->render('home/shop.html.twig', [
            'products' => $products,
            'search' => $form->createView()
        ]);
    }
}
