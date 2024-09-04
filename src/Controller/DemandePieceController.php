<?php

namespace App\Controller;

use App\Entity\DemandeType;
use App\Entity\DetailDemandePiece;
use App\Form\DetailDemandePieceType;
use App\Repository\DemandeTypeRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Service\DemandeTypeService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use function MongoDB\BSON\toJSON;

#[Route('/demande_piece')]
class DemandePieceController extends AbstractController
{
    #[Route(path: '/', name: 'dm_piece.liste_demande', methods: ['GET'])]
    public function index(DemandeTypeRepository $dm_rep): Response
    {
        $data = $dm_rep->findByEtat(40);
        return $this->render('demande_piece/ajout_piece_justificative.html.twig', [
            'demande_types' => $data
        ]);
    }

    #[Route(path: '/dm/{id}', name: 'dm_piece.show', methods: ['GET'])]
    public function show($id, DemandeTypeRepository $dm_rep): Response
    {
        $data = $dm_rep->find($id);
        return $this->render('demande_piece/_form_pj.html.twig', [
            'demande_type' => $data
        ]);
    }

    #[Route('/upload_file/{id}', name: 'dm.image')]
    public function uploadImage($id, Request $request,
                                DetailDemandePieceRepository $dt_dm_rep,
                                DemandeTypeService $dm_type_service)
    {
        $demande_user_id = 1;
        $type = $request->request->get('type');
        $file = $request->files->get('image');
        $montant_reel = $request->request->get('montant_reel');
        if (!$file) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Pas de fichier trouver'
            ]);
        }
        // Définir le répertoire de destination pour le fichier téléchargé
        // Assurez-vous que le paramètre 'uploads_directory' est défini dans config/services.yaml
        try {
            $newFilename = $dm_type_service->uploadImage($file, $this->getParameter('uploads_directory'));
            $rep = $dt_dm_rep->ajoutPieceJustificatif($id, $demande_user_id, $type, $newFilename, $montant_reel);
            $data = json_decode($rep->getContent(), true);

            $dm_type = $data['dm_type'];
            if ($data['success'] == true) {
                //dump($newFilename ."<------------ XXXXXXXXXX");

                return new JsonResponse([
                    'success' => true,
                    'message' => 'Upload reussi',
                    'path' => $this->generateUrl('dm_piece.liste_demande')
                ]);
            } else {
                return new JsonResponse([
                    'success' => false,
                    'message' => $data['message']
                ]);
            }
        } catch (\Exception $e) {
            dump('Erreur lors du téléchargement du fichier : ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
