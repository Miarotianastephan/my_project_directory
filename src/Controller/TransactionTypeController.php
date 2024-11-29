<?php

namespace App\Controller;

use App\Entity\TransactionType;
use App\Form\TransactionTypeType;
use App\Repository\TransactionTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/transaction/type')]
final class TransactionTypeController extends AbstractController
{
    #[Route(name: 'app_transaction_type_index', methods: ['GET'])]
    public function index(TransactionTypeRepository $transactionTypeRepository): Response
    {
        return $this->render('detail_transaction_compte/liste_code_transaction.html.twig', [
            'transaction_types' => $transactionTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_transaction_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transactionType = new TransactionType();
        $form = $this->createForm(TransactionTypeType::class, $transactionType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($transactionType);
            $entityManager->flush();

            return $this->redirectToRoute('app_transaction_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction_type/new.html.twig', [
            'transaction_type' => $transactionType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_type_show', methods: ['GET'])]
    public function show(TransactionType $transactionType): Response
    {
        return $this->render('transaction_type/show.html.twig', [
            'transaction_type' => $transactionType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TransactionType $transactionType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TransactionTypeType::class, $transactionType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_transaction_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction_type/edit.html.twig', [
            'transaction_type' => $transactionType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_type_delete', methods: ['POST'])]
    public function delete(Request $request, TransactionType $transactionType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transactionType->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($transactionType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transaction_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
