<?php

namespace App\Controller;

use App\Entity\Banque;
use App\Form\BanqueType;
use App\Repository\BanqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur dédié à la gestion des banques.
 *
 * Ce contrôleur permet de gérer les opérations CRUD (Créer, Lire, Mettre à jour, Supprimer) sur les entités `Banque`.
 * Il fournit des routes pour afficher la liste des banques, ajouter une nouvelle banque, afficher les détails d'une banque,
 * modifier une banque existante et supprimer une banque.
 */
#[Route('/banque')]
class BanqueController extends AbstractController
{
    /**
     * PAge pour afficher la liste de toutes les banques.
     *
     * Cette fonction permet d'afficher toutes les banques présentes dans la base de données.
     *
     * @param BanqueRepository $banqueRepository Le repository pour accéder aux données des banques.
     * @return Response La réponse contenant le rendu du template avec la liste des banques.
     */
    #[Route(name: 'app_banque_index', methods: ['GET'])]
    public function index(BanqueRepository $banqueRepository): Response
    {
        return $this->render('banque/index.html.twig', [
            'banques' => $banqueRepository->findAll(),
        ]);
    }

    /**
     * Crée une nouvelle banque.
     *
     * Cette fonction permet d'ajouter une nouvelle banque à la base de données. Le nom de la banque doit être unique.
     *
     * @param Request $request La requête HTTP contenant les données de la banque à ajouter.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entité pour persister les données.
     * @return Response La réponse redirigeant vers la liste des banques après la soumission du formulaire.
     */
    #[Route('/new', name: 'app_banque_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $banque = new Banque();
        $form = $this->createForm(BanqueType::class, $banque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($banque);
            $entityManager->flush();

            return $this->redirectToRoute('app_banque_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('banque/new.html.twig', [
            'banque' => $banque,
            'form' => $form,
        ]);
    }

    /**
     * Page pour afficher les détails d'une banque.
     *
     * Cette fonction permet d'afficher les informations détaillées d'une banque spécifique.
     *
     * @param Banque $banque L'entité `Banque` dont les détails doivent être affichés.
     * @return Response La réponse contenant le rendu du template avec les détails de la banque.
     */
    #[Route('/{id}', name: 'app_banque_show', methods: ['GET'])]
    public function show(Banque $banque): Response
    {
        return $this->render('banque/show.html.twig', [
            'banque' => $banque,
        ]);
    }

    /**
     * Page pour modifier une banque existante.
     *
     * Cette fonction permet de modifier les informations d'une banque existante. Le nom de la banque doit être unique.
     *
     * @param Request $request La requête HTTP contenant les données de la banque à modifier.
     * @param Banque $banque L'entité `Banque` à modifier.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entité pour persister les modifications.
     * @return Response La réponse redirigeant vers la liste des banques après la modification.
     */
    #[Route('/{id}/edit', name: 'app_banque_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Banque $banque, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BanqueType::class, $banque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_banque_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('banque/edit.html.twig', [
            'banque' => $banque,
            'form' => $form,
        ]);
    }

    /**
     * Supprime une banque.
     *
     * Cette fonction permet de supprimer une banque de la base de données.
     * La suppression est protégée par un token CSRF pour éviter les attaques.
     *
     * @param Request $request La requête HTTP contenant le token CSRF et l'ID de la banque à supprimer.
     * @param Banque $banque L'entité `Banque` à supprimer.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entité pour supprimer la banque.
     * @return Response La réponse redirigeant vers la liste des banques après la suppression.
     */
    #[Route('/{id}', name: 'app_banque_delete', methods: ['POST'])]
    public function delete(Request $request, Banque $banque, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $banque->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($banque);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_banque_index', [], Response::HTTP_SEE_OTHER);
    }
}
