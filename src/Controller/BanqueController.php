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

#[Route('/banque')]
class BanqueController extends AbstractController
{
    /**
     * Page de liste de toutes les banques
     **/
    #[Route(name: 'app_banque_index', methods: ['GET'])]
    public function index(BanqueRepository $banqueRepository): Response
    {
        return $this->render('banque/index.html.twig', [
            'banques' => $banqueRepository->findAll(),
        ]);
    }

    /**
     * Ajout de banque ($string nom_banque) :
     * * Le nom de banque doit être unique
     **/
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
     * Page de détails d'une banque.
     **/
    #[Route('/{id}', name: 'app_banque_show', methods: ['GET'])]
    public function show(Banque $banque): Response
    {
        return $this->render('banque/show.html.twig', [
            'banque' => $banque,
        ]);
    }

    /**
     * Page de modification de banque : le nom de banque doit être unique.
     **/
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
