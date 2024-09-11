<?php

namespace App\Controller;

use App\Entity\DemandeType;
use App\Repository\DemandeTypeRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Repository\LogDemandeTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route('/sg')]
class SGController extends AbstractController
{    
    private $user;

    public function __construct(Security $security)
    {
        // $this->security = $security;
        $this->user = $security->getUser(); 
    }

    #[Route(path: '/', name: 'SG.liste_demande_en_attente', methods: ['GET'])]
    public function index(DemandeTypeRepository $dm_Repository): Response
    {
        return $this->render('sg/index.html.twig', [
            //'demande_types' => $dm_Repository->findAll()->where($dm_Repository == 10)
            'demande_types' => $dm_Repository->findByEtat(10)
        ]);
    }

    #[Route('/{id}', name: 'SG.detail_demande_en_attente', methods: ['GET'])]
    public function show($id, DemandeTypeRepository $dm_Repository, DetailDemandePieceRepository $demandePieceRepository): Response
    {
        $data = $dm_Repository->find($id);
        $list_img = $demandePieceRepository->findByDemandeType($data);
        return $this->render('sg/show.html.twig',
            ['demande_type' => $data, 'images' => $list_img]
        );
    }

    #[Route('/demande/valider/{id}', name: 'SG.valider_en_attente', methods: ['GET'])]
    public function valider($id, DemandeTypeRepository $dm_Repository): Response
    {
        $data = $dm_Repository->find($id);
        if (!$data){
            return new JsonResponse([
                'success' => false,
                'message' => 'Demande introuvable',
            ]);
        }
        $plan_compte = $data -> getEntityCode();
        $montant = 20000 ;
        return $this->render('sg/valider_demande.html.twig', ['demande_type' => $data,'montant'=>$montant]);
    }

    #[Route('/demande/refuser/{id}', name: 'SG.refus_demande_en_attente', methods: ['GET'])]
    public function refuser($id, DemandeTypeRepository $dm_Repository): Response
    {
        $data = $dm_Repository->find($id);
        return $this->render('sg/refuser_demande.html.twig', ['demande_type' => $data]);
    }

    #[Route('/valider_demande/{id}', name: 'valider_demande', methods: ['POST'])]
    public function valider_demande($id, LogDemandeTypeRepository $logDemandeTypeRepository): JsonResponse
    {
        $id_user_sg = $this->user->getId(); // mandeha io 
        
        $rep = $logDemandeTypeRepository->ajoutValidationDemande($id, $id_user_sg);
        $data = json_decode($rep->getContent(), true);
        if ($data['success'] == true) {
            return new JsonResponse([
                'success' => true,
                'message' => 'validation réussi',
                'path' => $this->generateUrl('SG.liste_demande_en_attente')
            ]);
        } else {
            return new JsonResponse([
                'success' => true,
                'message' => $data['message']
            ]);
        }
    }

    #[Route('/refuser_demande/{id}', name: 'refuser_demande', methods: ['POST', 'GET'])]
    public function refuser_demande($id, Request $request, LogDemandeTypeRepository $logDemandeTypeRepository): JsonResponse
    {

        $id_user_sg = $this->user->getId();
        $data = json_decode($request->getContent(), true);
        $commentaire_data = $data['commentaire'] ?? null;

        $rep = $logDemandeTypeRepository->ajoutRefuserDemande($id, $id_user_sg, $commentaire_data);

        $data = json_decode($rep->getContent(), true);
        dump($data['success']);

        if ($data['success'] == true) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Pas de commentaire reçu ',
                'path' => $this->generateUrl('SG.liste_demande_en_attente')
            ]);
        } else {
            return new JsonResponse([
                'success' => true,
                'message' => 'Pas de commentaire reçu '
            ]);
        }

    }
}

