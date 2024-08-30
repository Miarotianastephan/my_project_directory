<?php

namespace App\Controller;

use App\Entity\GroupeUtilisateur;
use App\Form\GroupeUtilisateurType;
use App\Repository\GroupeUtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/groupe/utilisateur')]
class GroupeUtilisateurController extends AbstractController
{
    #[Route('/', name: 'app_groupe_utilisateur_index', methods: ['GET'])]
    public function index(GroupeUtilisateurRepository $groupeUtilisateurRepository): Response
    {
        return $this->render('groupe_utilisateur/group_users.html.twig', [
            'groupe_utilisateurs' => $groupeUtilisateurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_groupe_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $groupeUtilisateur = new GroupeUtilisateur();
        $form = $this->createForm(GroupeUtilisateurType::class, $groupeUtilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($groupeUtilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('app_groupe_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe_utilisateur/new.html.twig', [
            'groupe_utilisateur' => $groupeUtilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_groupe_utilisateur_show', methods: ['GET'])]
    public function show(GroupeUtilisateur $groupeUtilisateur): Response
    {
        return $this->render('groupe_utilisateur/show.html.twig', [
            'groupe_utilisateur' => $groupeUtilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_groupe_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GroupeUtilisateur $groupeUtilisateur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroupeUtilisateurType::class, $groupeUtilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_groupe_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe_utilisateur/edit.html.twig', [
            'groupe_utilisateur' => $groupeUtilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_groupe_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, GroupeUtilisateur $groupeUtilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$groupeUtilisateur->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($groupeUtilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_groupe_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }
}
