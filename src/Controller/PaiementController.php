<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Produit;
use App\Entity\Commande;
use Stripe\Checkout\Session;
use App\Service\PanierService;
use App\Entity\CommandeProduit;
use App\Entity\User;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class PaiementController extends AbstractController
{
    #[Route('/paiement', name: 'app_paiement')]
    public function index(PanierService $panierService, EntityManagerInterface $em): Response
    {

        // Stripe secret key
        $ssk = $this->getParameter('stripe.secretKey');
        Stripe::setApiKey($ssk); // On configure Stripe
        $tableauPourStripe = []; // Un tableau pour Stripe
        $user = $this->getUser();

        // Créer la commande
        $commande = new Commande;
        $commande->setUser($user);
        $commande->setDate(new \DateTimeImmutable());
        $commande->setTotal($panierService->total());
        $commande->setEtat('En attente de paiement');
        $commande->setToken(
            hash('sha256', random_bytes(32)) // Crée une chaîne de caractères aléatoire
        );

        $panier = $panierService->showCart();


        // On boucle pour remplir la commande
        // On boucle également pour remplir le formulaire Stripe
        foreach ($panier as $id => $ligne) {

            $quantity = $ligne['quantity'];
            /** @var Produit */
            $produit = $ligne['produit'];

            // On remplit la commande
            $cp = new CommandeProduit;
            $cp->setQuantite($quantity);
            $cp->setProduit($produit);

            $commande->addCommandeProduit($cp);


            // On remplit le tableau pour Stripe

            $tableauPourStripe[] = [
                'quantity' => $quantity,
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $produit->getNom(),
                        // 'images' =>  [$produit->getImage() ? $this->getParameter('base_url') . '/uploads/' . $produit->getImage() : 'https://plchldr.co/i/500x500'] // Lien ABSOLU (qui commence par "http(s)://") ; Pas obligatoire
                    ],
                    'unit_amount' => $produit->getPrix() * 100 // Prix en CENTIMES
                ]
            ];
        }

        // On sauvegarde la commande en BDD
        $em->persist($commande);
        $em->flush(); // On enregistre la commande en BDD
        // dd($commande);

        $checkout = Session::create([
            'line_items' => $tableauPourStripe,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_paiement_success', [
                'token' => $commande->getToken(),
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_paiement_fail', [
                'commande' => $commande->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL),  // Lien ABSOLU (qui commence par "http(s)://")
        ]);

        return $this->redirect($checkout->url);
    }

    /**
     * Le paiement a réussi
     * On "valide" la commande
     */
    #[Route('/paiement/success/{token}', name: 'app_paiement_success')]
    public function apres(string $token, CommandeRepository $commandeRepository, PanierService $panierService, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $commande = $commandeRepository->findOneBy(['token' => $token]);
        $commande->setEtat('Validée');
        $em->persist($commande);
        $em->flush();
        
        // On vide le panier
        $panierService->clear();
        
        // Récupérer l'utilisateur
        /** @var User|null */
        $user = $this->getUser();

        // Récupérer les CommandeProduits de la commande
        $commandeProduits = $commande->getCommandeProduits();

        // Initialiser le tableau pour les produits
        $produits = [];

        // Calculer le prix total de la commande
        $prixTotal = 0;

        foreach ($commandeProduits as $commandeProduit) {
            $produit = $commandeProduit->getproduit();
            $prix = $produit->getPrix();
            $quantite = $commandeProduit->getQuantite();

            $produits[] = [
                'produit' => $produit,
                'quantite' => $quantite,
                'prix' => $prix,
                'sousTotal' => $prix * $quantite,
            ];

            $prixTotal += $prix * $quantite;
        }

        $email = (new TemplatedEmail())
            ->from(new Address("admin@sneakpeek.fr", "Sneakpeek Assist Bot"))
            ->to($user->getEmail())
            ->subject('Merci de votre achat')
            ->htmlTemplate('emails/facture.html.twig')
            ->context([
                'produits' => $produits,
                'prixTotal' => $prixTotal,
                'commande' => $commande,
                'user' => $user,
            ]);

        $mailer->send($email);

        return $this->render('paiement/success.html.twig');
    }

    /**
     * Le paiement a échoué
     * On supprime la commande
     */
    #[Route('/paiement/echec/{commande}', name: 'app_paiement_fail')]
    public function return(Commande $commande, PanierService $panierService, EntityManagerInterface $em): Response
    {
        $panierService->clear($commande);
        $em->remove($commande);
        $em->flush();
        // dd($commande);

        return $this->render('paiement/cancel.html.twig');
    }
}
