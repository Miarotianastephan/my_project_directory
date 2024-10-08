<?php

namespace App\Controller;

use App\Entity\DetailTransactionCompte;
use App\Form\DetailTransactionCompteType;
use App\Repository\DetailTransactionCompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/detail/transaction/compte')]
final class DetailTransactionCompteController extends AbstractController
{
    #[Route(name: 'app_detail_transaction_compte_index', methods: ['GET'])]
    public function index(DetailTransactionCompteRepository $detailTransactionCompteRepository): Response
    {
        return $this->render('detail_transaction_compte/index.html.twig', [
            'detail_transaction_comptes' => $detailTransactionCompteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_detail_transaction_compte_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        dump($request);
        $detailTransactionCompte = new DetailTransactionCompte();
        $form = $this->createForm(DetailTransactionCompteType::class, $detailTransactionCompte);
        $form->handleRequest($request);



        if ($form->isSubmitted()
            && $form->isValid()
        ) {

            $entityManager->persist($detailTransactionCompte);
            $entityManager->flush();
            return $this->redirectToRoute('app_detail_transaction_compte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('detail_transaction_compte/new.html.twig', [
            'detail_transaction_compte' => $detailTransactionCompte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_detail_transaction_compte_show', methods: ['GET'])]
    public function show(DetailTransactionCompte $detailTransactionCompte): Response
    {
        return $this->render('detail_transaction_compte/show.html.twig', [
            'detail_transaction_compte' => $detailTransactionCompte,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_detail_transaction_compte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DetailTransactionCompte $detailTransactionCompte, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DetailTransactionCompteType::class, $detailTransactionCompte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_detail_transaction_compte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('detail_transaction_compte/edit.html.twig', [
            'detail_transaction_compte' => $detailTransactionCompte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_detail_transaction_compte_delete', methods: ['POST'])]
    public function delete(Request $request, DetailTransactionCompte $detailTransactionCompte, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$detailTransactionCompte->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($detailTransactionCompte);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_detail_transaction_compte_index', [], Response::HTTP_SEE_OTHER);
    }
}
