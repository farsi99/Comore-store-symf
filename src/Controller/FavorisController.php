<?php

namespace App\Controller;

use App\Repository\HearthRepository;
use App\Services\HearthService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/favoris")
 */
class FavorisController extends AbstractController
{

    private $favoris;

    public function __construct(HearthService $favoris)
    {
        $this->favoris = $favoris;
    }

    /**
     * @Route("/", name="favoris")
     * 
     */
    public function index(HearthRepository $repo): Response
    {

        $user = $this->getUser();
        $favoris = $repo->findBy(['author' => $user]);
        return $this->render('favoris/index.html.twig', [
            'favoris' => $favoris,
        ]);
    }

    /**
     * cette méthode traite l'ajout en favoris     * 
     * @Route("/ajout-favoris/{id}", name="add_favoris")
     * 
     */
    public function addFavoris(Request $request, $id)
    {
        $user = $this->getUser();
        $this->favoris->addFavoris($id, $user);
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Delete de favoris
     * @Route("/supprimer-favoris/{id}",name="delete_favoris")
     */
    public function delete($id)
    {
        $this->favoris->deleteFavoris($id, $this->getUser());
        return $this->redirectToRoute('favoris');
    }
}
