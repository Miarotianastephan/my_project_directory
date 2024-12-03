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
class TransactionTypeController extends AbstractController
{

    /**
     * Page de liste des codes de transaction.
     *
     * @param TransactionTypeRepository $transactionTypeRepository
     * @return Response
     */
    #[Route(name: 'app_transaction_type_index', methods: ['GET'])]
    public function index(TransactionTypeRepository $transactionTypeRepository): Response
    {
        return $this->render('detail_transaction_compte/liste_code_transaction.html.twig', [
            'transaction_types' => $transactionTypeRepository->findAll(),
        ]);
    }

    /**
     * Page de détail de code de transaction.
     *
     * @param TransactionType $transactionType
     * @return Response
     */
    #[Route('/{id}', name: 'app_transaction_type_show', methods: ['GET'])]
    public function show(TransactionType $transactionType): Response
    {
        return $this->render('transaction_type/show.html.twig', [
            'transaction_type' => $transactionType,
        ]);
    }


    #[Route('/{id}', name: 'app_transaction_type_delete', methods: ['POST'])]
    public function delete(Request $request, TransactionType $transactionType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $transactionType->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($transactionType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transaction_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
