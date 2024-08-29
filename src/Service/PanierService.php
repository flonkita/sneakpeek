<?php

namespace App\Service;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class PanierService {

    protected $session;
    protected $produitRepository;

    public function __construct( RequestStack $rs, ProduitRepository $produitRepository)
    {
        $this->session = $rs->getSession();
        $this->produitRepository = $produitRepository;
    }

    public function showCart(): array {
        // On récupère la session 'panier' si elle existe - sinon elle est créée avec un tableau vide
        $panier = $this->session->get('panier', []);

        // Variable tableau
        $panierData = [];
        
        // On boucle sur la session 'panier' pour récupérer proprement l'objet (au lieu de l'id) et la quantité
        foreach($panier as $id => $quantity)
        {
            $produit = $this->produitRepository->find($id);
            if ($produit) {
                $panierData[] = [
                    "produit" => $produit,
                    "quantity" => $quantity
                ];
            }
        }
        return $panierData;
    }

    public function total(): float {
        // On calcule le total du panier ici, afin de ne pas avoir à le faire dans la vue Twig
        $total = 0;

        foreach($this->showCart() as $item)
        {
            $total += $item['produit']->getPrix() * $item['quantity'];
        }
        // On calcule le total du prix du produit * le nb de produits

        return $total;
    }

    public function add(int $id) {
        // ETAPE 1 : On récupère la session 'panier' si elle existe - sinon elle est créée avec un tableau vide
        $panier = $this->session->get('panier', []);

        // ETAPE 2 : On ajoute la quantité 1, au produit d'id $id
        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        // ETAPE 3 : On remplace la variable de session panier par le nouveau tableau $panier
        $this->session->set('panier', $panier);
    }

    public function remove(int $id) {
        // On récupère la session 'panier' si elle existe - sinon elle est créée avec un tableau vide
        $panier = $this->session->get('panier', []);
        
        // On supprime de la session celui dont on a passé l'id
        if (!empty($panier[$id])) {
            $panier[$id]--;

            if ($panier[$id] <= 0) {
                unset($panier[$id]); // unset pour dépiler de la session
            }
        }

        // On réaffecte le nouveau panier à la session
        $this->session->set('panier', $panier);
    } 

    public function clear() {
        $this->session->remove('panier'); 
    }
}