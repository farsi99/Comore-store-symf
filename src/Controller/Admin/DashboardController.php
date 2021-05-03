<?php

namespace App\Controller\Admin;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Alert;
use App\Entity\Blog;
use App\Entity\Order;
use App\Entity\Hearth;
use App\Entity\Carrier;
use App\Entity\CategorieArticle;
use App\Entity\Contact;
use App\Entity\Product;
use App\Entity\Categories;
use App\Entity\HomeSlider;
use App\Entity\Pages;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;


class DashboardController extends AbstractDashboardController
{


    /**
     * @Route("/admin", name="admin")
     * 
     */
    public function index(): Response
    {
        return $this->render('bundles/EasyAdminBundle/welcome.html.twig', []);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Comores Store');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('assets/css/easyadmin.css');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToCrud('Produits', 'fas fa-box', Product::class);
        yield MenuItem::linkToCrud('Commande', 'fa fa-shopping-bag', Order::class);
        yield MenuItem::linkToCrud('Panier', 'fa fa-shopping-cart', Cart::class);
        yield MenuItem::linkToCrud('Favoris', 'fas fa-heart', Hearth::class);
        yield MenuItem::linkToCrud('Alerte stock', 'fas fa-bell', Alert::class);
        yield MenuItem::linkToCrud('Catégorie', 'fa fa-list-alt', Categories::class);
        yield MenuItem::linkToCrud('Clients', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Transporteur', 'fa fa-truck', Carrier::class);
        yield MenuItem::linkToCrud('Slider', 'fa fa-images', HomeSlider::class);
        yield MenuItem::linkToCrud('Contact', 'fa fa-envelope', Contact::class);

        yield MenuItem::section('Site vitrine', 'fas fa-chevron-circle-down')->setCssClass('sous-menu');

        yield MenuItem::section('Blog');
        yield MenuItem::linkToCrud('Categories', 'fa fa-tags', CategorieArticle::class);
        yield MenuItem::linkToCrud('Blog', 'fa fa-file-text', Blog::class);
        yield MenuItem::linkToCrud('Pages', 'fa fa-th', Pages::class);
        yield MenuItem::linkToRoute('Gestion des menus', 'fa fa-list', 'admin_menu');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getUsername())
            ->displayUserName(true)
            //->setAvatarUrl('https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_1280.png')
            //->setAvatarUrl($user->getProfileImageUrl())
            ->displayUserAvatar(true)
            ->setGravatarEmail($user->getUsername())

            ->addMenuItems([
                //MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
            ]);
    }
}
