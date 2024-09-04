<?php

namespace App\Controller;

use App\Entity\DemandeType;
use App\Entity\LogDemandeType;
use App\Entity\Utilisateur;
use App\Repository\DemandeTypeRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Repository\LogDemandeTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tresorier')]
class TresorierController extends AbstractController
{
    #[Route('/', name: 'tresorier.liste_demande_en_attente', methods: ['GET'])]
    public function index(DemandeTypeRepository $dm_Repository): Response
    {
        return $this->render('tresorier/index.html.twig', [
            'demande_types' => $dm_Repository->findByEtat(20)
        ]);

    }

    #[Route('/demande/{id}', name: 'tresorier.detail_demande_en_attente', methods: ['GET'])]
    public function show($id, EntityManagerInterface $entityManager, DetailDemandePieceRepository $demandePieceRepository): Response
    {
        $data = $entityManager->find(DemandeType::class, $id);
        $list_img = $demandePieceRepository->findByDemandeType($data);
        return $this->render('tresorier/show.html.twig', ['demande_type' => $data, 'images' => $list_img]);
    }

    #[Route('/demande/valider/{id}', name: 'tresorier.valider_fond', methods: ['GET'])]
    public function valider_fond($id, EntityManagerInterface $entityManager): Response
    {
        $data = $entityManager->find(DemandeType::class, $id);
        return $this->render('tresorier/deblocker_fond.html.twig', ['demande_type' => $data]);
    }

    #[Route('/remettre_fond/{id}', name: 'tresorier.remettre_fond', methods: ['POST'])]
    public function remettre_fond($id, LogDemandeTypeRepository $logDemandeTypeRepository): JsonResponse
    {

        $id_user_tresorier = 3;
        $rep = $logDemandeTypeRepository->ajoutDeblockageFond($id, $id_user_tresorier);
        $data = json_decode($rep->getContent(), true);
        if ($data['success'] == true) {
            return new JsonResponse([
                'success' => true,
                'message' => 'La demande a été remis',
                'path' => $this->generateUrl('tresorier.liste_demande_en_attente')
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => $data['message']
            ]);
        }
    }
}
