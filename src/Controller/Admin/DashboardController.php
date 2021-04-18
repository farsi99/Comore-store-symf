<?php

namespace App\Controller\Admin;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Carrier;
use App\Entity\Product;
use App\Entity\Categories;
use App\Entity\Contact;
use App\Entity\HomeSlider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Comores Store');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToCrud('Produits', 'fas fa-box', Product::class);
        yield MenuItem::linkToCrud('Commande', 'fa fa-shopping-bag', Order::class);
        yield MenuItem::linkToCrud('Panier', 'fa fa-shopping-cart', Cart::class);
        yield MenuItem::linkToCrud('Catégorie', 'fa fa-list-alt', Categories::class);
        yield MenuItem::linkToCrud('Transporteur', 'fa fa-truck', Carrier::class);
        yield MenuItem::linkToCrud('Slider', 'fa fa-images', HomeSlider::class);
        yield MenuItem::linkToCrud('Contact', 'fa fa-envelope', Contact::class);
    }
}
