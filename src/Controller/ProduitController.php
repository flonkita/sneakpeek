<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class ProduitController extends AbstractController
{
    #[Route("/produit/{id}", name:"produit_detail", requirements:["id"=>"\d+"])]
    public function detail($id, ManagerRegistry $doctrine)
    {
        #Etape 1 : Récupérer un Produit
        $produit = $doctrine->getRepository(Produit::class)->find($id);

        return $this->render('Produit/index.html.twig', [
            "produit" => $produit
        ]);
    }
}
