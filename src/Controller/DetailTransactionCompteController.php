<?php

namespace App\Controller;

use App\Entity\DetailTransactionCompte;
use App\Form\DetailTransactionCompteType;
use App\Repository\DetailTransactionCompteRepository;
use App\Repository\TransactionTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/detail/transaction/compte')]
class DetailTransactionCompteController extends AbstractController
{
    /**
     * Page de liste de tous les codes de transaction
     *
     * @param DetailTransactionCompteRepository $detailTransactionCompteRepository
     * @return Response
     */
    #[Route(name: 'app_detail_transaction_compte_index', methods: ['GET'])]
    public function index(DetailTransactionCompteRepository $detailTransactionCompteRepository): Response
    {
        return $this->render('detail_transaction_compte/index.html.twig', [
            'detail_transaction_comptes' => $detailTransactionCompteRepository->findAll(),
        ]);
    }

    /**
     * Page de liaison entre code de transaction et plan de compte avec le type : crédit/débit.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param TransactionTypeRepository $transactionTypeRepository
     * @return Response
     */
    #[Route('/add', name: 'app_detail_transaction_compte_add', methods: ['GET'])]
    public function add(Request $request, EntityManagerInterface $entityManager, TransactionTypeRepository $transactionTypeRepository): Response
    {
        return $this->render('detail_transaction_compte/_add.html.twig');
    }

    /**
     * Ajout de liaison entre code de transaction et plan de compte avec le type dans la base de donnée.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param TransactionTypeRepository $transactionTypeRepository
     * @return Response
     */
    #[Route('/add_transaction', name: 'app_detail_transaction_compte_add_transaction', methods: ['POST'])]
    public function add_transaction(Request $request, EntityManagerInterface $entityManager, TransactionTypeRepository $transactionTypeRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $code_transaction = $data['code_transaction'] ?? null;
        $libelle = $data['libelle'] ?? null;
        $description = $data['description'] ?? null;
        $reponse = $transactionTypeRepository->ajoutCodeTransaction($code_transaction, $libelle, $description);

        $reponse = json_decode($reponse->getContent(), true);

        return new JsonResponse([
            'success' => $reponse['success'],
            'message' => $reponse['message'],
            'url' => $this->generateUrl('app_detail_transaction_compte_index')
        ]);
    }

    /**
     * Ajout et sauvegarde de liaison entre code de transaction et plan de compte avec le type
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param DetailTransactionCompteRepository $detailTransactionCompteRepository
     * @return Response
     */
    #[Route('/new', name: 'app_detail_transaction_compte_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, DetailTransactionCompteRepository $detailTransactionCompteRepository): Response
    {
        $detailTransactionCompte = new DetailTransactionCompte();
        $form = $this->createForm(DetailTransactionCompteType::class, $detailTransactionCompte);
        $form->handleRequest($request);

        if ($form->isSubmitted()
            && $form->isValid()
        ) {
            $is_existe = $detailTransactionCompteRepository->findByTransactionAndPlanCompte($detailTransactionCompte->getTransactionType(), $detailTransactionCompte->getPlanCompte());
            dump($is_existe);
            if ($is_existe !== null) {
                return $this->render('detail_transaction_compte/new.html.twig', [
                    'detail_transaction_compte' => $detailTransactionCompte,
                    'form' => $form,
                    'message' => "Cette information éxiste déjà",
                ]);
            } else {
                $entityManager->persist($detailTransactionCompte);
                $entityManager->flush();
                return $this->redirectToRoute('app_detail_transaction_compte_index', [], Response::HTTP_SEE_OTHER);
            }


        }

        return $this->render('detail_transaction_compte/new.html.twig', [
            'detail_transaction_compte' => $detailTransactionCompte,
            'form' => $form,
            'message' => null,
        ]);
    }

    /**
     * Page de détails de code de transaction.
     *
     * @param DetailTransactionCompte $detailTransactionCompte
     * @return Response
     */
    #[Route('/{id}', name: 'app_detail_transaction_compte_show', methods: ['GET'])]
    public function show(DetailTransactionCompte $detailTransactionCompte): Response
    {
        return $this->render('detail_transaction_compte/show.html.twig', [
            'detail_transaction_compte' => $detailTransactionCompte,
        ]);
    }

    /**
     * Page de modification et sauvegarde de modification de liaison entre code de transaction et plan de compte avec le type
     *
     * @param Request $request
     * @param DetailTransactionCompte $detailTransactionCompte
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

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

    /**
     * Suppression de liaison entre code de transaction et plan de compte
     *
     * @param Request $request
     * @param DetailTransactionCompte $detailTransactionCompte
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'app_detail_transaction_compte_delete', methods: ['POST'])]
    public function delete(Request $request, DetailTransactionCompte $detailTransactionCompte, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $detailTransactionCompte->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($detailTransactionCompte);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_detail_transaction_compte_index', [], Response::HTTP_SEE_OTHER);
    }
}
