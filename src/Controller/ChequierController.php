<?php

namespace App\Controller;

use App\Entity\Chequier;
use App\Form\ChequierType;
use App\Repository\ChequierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chequier')]
final class ChequierController extends AbstractController
{
    #[Route(name: 'app_chequier_index', methods: ['GET'])]
    public function index(ChequierRepository $chequierRepository): Response
    {
        return $this->render('chequier/index.html.twig', [
            'chequiers' => $chequierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_chequier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chequier = new Chequier();
        $form = $this->createForm(ChequierType::class, $chequier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chequier);
            $entityManager->flush();

            return $this->redirectToRoute('app_chequier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chequier/new.html.twig', [
            'chequier' => $chequier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chequier_show', methods: ['GET'])]
    public function show(Chequier $chequier): Response
    {
        return $this->render('chequier/show.html.twig', [
            'chequier' => $chequier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chequier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chequier $chequier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChequierType::class, $chequier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_chequier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chequier/edit.html.twig', [
            'chequier' => $chequier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chequier_delete', methods: ['POST'])]
    public function delete(Request $request, Chequier $chequier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chequier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($chequier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chequier_index', [], Response::HTTP_SEE_OTHER);
    }
}
