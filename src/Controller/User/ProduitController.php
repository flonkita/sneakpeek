<?php

namespace App\Controller\User;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'user_produit_')]
class ProduitController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('user/produit/index.html.twig', [
            'produit' => $produitRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        #Etape 1 : Créer un objet vide
        $produit = new Produit;

        #Etape 2 : Créer le formulaire
        $formProduit = $this->createForm(ProduitType::class, $produit);

        $formProduit->handleRequest($request);
        if ($formProduit->isSubmitted() && $formProduit->isValid()) {
            $data = $formProduit->getData();

            if ($formProduit->get('image')->getData() == null) {
                $image = null;
            } else {
                $image = $formProduit->get('image')->getData()->getClientOriginalName();
            }
            if ($image) {
                $image = $formProduit->get('image')->getData()->getClientOriginalName();
                $data->setImage($image);
                $formProduit->get('image')->getData()->move(
                    $this->getParameter('images_directory'),
                    $image
                );
            }
            #Etape 1 : On appel l'entity manager de doctrine
            $entityManager = $doctrine->getManager();

            #Etape 3 : on indique a doctrine que l'on souhaite préparer l'enregistrement d'un nouvel élément
            $entityManager->persist($data);

            #etape 4: on valide a doctrine que l'on veut enregisterer/persister en BDD
            $entityManager->flush();
            #etape 5: on affiche ou on redirge vers une autre page
            $session->getFlashBag()->add('create_article', 'Un produit a bien été ajouté.');
            return $this->redirectToRoute('user_article_index');
        }

        #Etape 3 : On envoie le formulaire dans la vue
        return $this->render('user/produit/new.html.twig', [
            'formProduit' => $formProduit->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Produit $produit,ManagerRegistry $doctrine, ProduitRepository $produitRepository, SessionInterface $session): Response
    {
        $entityManager = $doctrine->getManager();
        $formProduit = $this->createForm(ProduitType::class, $produit);
        $formProduit->handleRequest($request);

        if ($formProduit->isSubmitted() && $formProduit->isValid()) {
            $data = $formProduit->getData();

            if ($formProduit->get('image')->getData() == null) {
                $image_name = $produit->getImage();
            } else {
                $image_name = $formProduit->get('image')->getData()->getClientOriginalName();
                $image_name = uniqid() . $image_name;
                $formProduit->get('image')->getData()->move(
                    $this->getParameter('images_directory'),
                    $image_name
                );
            }


            if ($image_name) {
                $data->setImage($image_name);
            }

            $entityManager->persist($data);
            $entityManager->flush();
            $produitRepository->add($produit, true);

            $session->getFlashBag()->add('edit_article', 'Un produit a bien été modifié.');
            return $this->redirectToRoute('user_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/produit/edit.html.twig', [
            'produit' => $produit,
            'formProduit' => $formProduit,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete")
     */
    public function delete($id, Produit $produit, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        #Etape 1 : On appelle l'entity manager de doctrine
        $entityManager = $doctrine->getManager();

        #Etape 2 : On récupère (grâce au repository de doctrine) l'objet que l'on souhaite modifier
        $produit = $doctrine->getRepository(Produit::class)->find($id);

        #Etape 3 : On supprime à l'aide de l'entity manager
        $entityManager->remove($produit);

        #Etape 4 : On valide les modifications
        $entityManager->flush();

        $session->getFlashBag()->add('delete_article', 'Un produit a bien été supprimé.');

        return $this->redirectToRoute('user_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
