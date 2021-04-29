<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LoaderController extends AbstractController
{

    /**
     * @Route("/loader", name="update_user")
     */
    public function updateUser(EntityManagerInterface $manager, UserRepository $repo)
    {
        $user = $repo->findOneBy(['id' => 1]);
        if ($user) {
            $user->setRoles(['ROLE_ADMIN']);
            $manager->persist($user);
            $manager->flush();
            return new JsonResponse($user, 200);
        }
    }
}
