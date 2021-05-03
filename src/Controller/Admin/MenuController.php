<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class MenuController extends AbstractDashboardController
{
    /**
     * @Route("/admin/menu", name="admin_menu")
     */
    public function index(): Response
    {
        return $this->render('bundles/EasyAdminBundle/menu.html.twig', []);
    }
}
