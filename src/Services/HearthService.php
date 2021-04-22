<?php

namespace App\Services;

use App\Entity\Hearth;
use App\Repository\HearthRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class HearthService
{

    private $manager;
    private $repoprod;
    private $session;
    private $repofavoris;

    public function __construct(EntityManagerInterface $manager, ProductRepository $repoprod, SessionInterface $session, HearthRepository $repofavoris)
    {
        $this->manager = $manager;
        $this->repoprod = $repoprod;
        $this->session = $session;
        $this->repofavoris = $repofavoris;
    }

    /**
     * Ajout des produits en favoris
     */
    public function addFavoris($idProd, $user)
    {
        $product = $this->repoprod->findOneBy(['id' => $idProd]);
        //On verifie si ce produit de cet user est deja en favoris
        if ($product) {
            $isFavoris = $this->repofavoris->findBy(['product' => $product, 'author' => $user]);
            if ($isFavoris) {
                //ce produit est deja en favoris
            } else {
                $favoris = new Hearth();
                $favoris->setProduct($product)
                    ->setAuthor($user);
                $this->manager->persist($favoris);
                $this->manager->flush();
                $this->addSessionFavoris($user);
            }
        }
    }

    /**
     * cette méthode ajoute en session
     */
    public function addSessionFavoris($user)
    {
        //On ajoute tout en session
        $this->updateFavoris($user);
    }

    /**
     * Cette méthode traite la suppression en favoris
     */
    public function deleteFavoris($idProd, $user)
    {
        $product = $this->repoprod->findOneBy(['id' => $idProd]);
        $favoris = $this->repofavoris->findOneBy(['author' => $user, 'product' => $product]);
        if ($favoris) {
            $this->manager->remove($favoris);
            $this->manager->flush();
        }
        $this->updateFavoris($user);
    }

    /**
     * Ajout du produit favoris en session
     */
    public function addSessionFav($favoris)
    {
        $this->session->set('favoris', $favoris);
    }


    /**
     * Supprimer tous les produits du panier
     */
    public function deleteSessionFavoris()
    {
        $this->session->set('favoris', null);
    }

    /**
     * Mise à jour du panier
     */
    public function updateFavoris($user)
    {
        $this->deleteSessionFavoris();
        $allfavoris = $this->repofavoris->findBy(['author' => $user]);
        $this->session->set('favoris', $allfavoris);
    }

    public function getSessionFavoris()
    {
        return $favoris = $this->session->get('favoris');
    }

    public function redirectBack()
    {
    }
}
