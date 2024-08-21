<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sg')]
class SGController extends AbstractController {

    #[Route(path: '/', name: 'SG.index.liste_demande_en_attente', methods: ['GET'])]
    public function index() : Response {
        $data = [
            [
                'dm_id' => 1,
                'dm_date' => new \DateTime('2024-01-20'),
                'dm_montant' => 10000,
                'ref_demande' => 'REF_001',
                'demande' => [
                    'libelle' => 'libel1'
                ]
            ],
            [
                'dm_id' => 2,
                'dm_date' => new \DateTime('2025-01-20'),
                'dm_montant' => 5000,
                'ref_demande' => 'REF_002',
                'demande' => [
                    'libelle' => 'libel2'
                ]
            ],
            [
                'dm_id' => 3,
                'dm_date' => new \DateTime('2026-01-20'),
                'dm_montant' => 1000000,
                'ref_demande' => 'REF_003',
                'demande' => [
                    'libelle' => 'libel3'
                ]
            ]
        ];
        return $this->render('sg/index.html.twig', [
            'demande_types' => $data
        ]);
    }
    #[Route('/{id}', name: 'SG.detail_demande_en_attente', methods: ['GET'])]

    public function show(Demande $groupeUtilisateur): Response
    {
        return $this->render('groupe_utilisateur/show.html.twig', [
            'groupe_utilisateur' => $groupeUtilisateur,
        ]);
    }
}

