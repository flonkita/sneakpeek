<?php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(PanierService $panierService): Response
    {
        return $this->render('panier/index.html.twig', [
            'items' => $panierService->showCart(),
            'total' => $panierService->total()
        ]);
    }

    #[Route('/panier/add/{id}', name: 'panier_add')]
    public function add(int $id, PanierService $panierService): Response
    {
        $panierService->add($id);
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/remove/{id}', name: 'panier_remove')]
    public function remove(int $id, PanierService $panierService): Response
    {
        $panierService->remove($id);
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/clear', name: 'panier_clear')]
    public function clear(PanierService $panierService): Response
    {
        $panierService->clear();
        return $this->redirectToRoute('app_panier');
    }
}