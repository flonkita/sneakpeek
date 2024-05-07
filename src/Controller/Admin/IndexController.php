<?php

namespace App\Controller\Admin;

use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository, ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();
        $users = $userRepository->findAll();
        // $commande = $commandeRepository->findBy(['etat' => 'Validée']);
        return $this->render('admin/home/index.html.twig', [
            'produits' => $produits,
            'users' => $users,
        ]);
    }
}
