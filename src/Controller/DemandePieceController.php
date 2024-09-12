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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use function MongoDB\BSON\toJSON;

#[Route('/demande_piece')]
class DemandePieceController extends AbstractController
{
    private $user;
    private $megaByte = 1048576;

    public function __construct(Security $security)
    {
        // $this->security = $security;
        $this->user = $security->getUser();
    }

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
        $demande_user_id = $this->user->getId();
        $type = $request->request->get('type');
        $montant_reel = $request->request->get('montant_reel');


        $files = $request->files->get('image');

        if (empty($files)) {
            return $this->json([
                'success' => false,
                'message' => 'Aucun fichier trouvé'
            ]);
        }
        $uploadedFiles = [];
        $errors = [];


        foreach ($files as $file) {
            if ($file) {
                //taille de chaque fichier
                // Récupérer la taille du fichier en octets
                $fileSizeInBytes = $file->getSize() / $this->megaByte;
                dump("La taille du fichier=" . $fileSizeInBytes . "Mb \n le nom du fichier=" . $file->getClientOriginalName());
                if ($fileSizeInBytes > 1) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Le fichier ' . $file->getClientOriginalName() . ' est trop volimineux'
                    ]);
                }
            }
        }
        // Définir le répertoire de destination pour le fichier téléchargé
        // Assurez-vous que le paramètre 'uploads_directory' est défini dans config/services.yaml
        foreach ($files as $file) {
            try {
                $newFilename = $dm_type_service->uploadImage($file, $this->getParameter('uploads_directory'));
                $rep = $dt_dm_rep->ajoutPieceJustificatif($id, $demande_user_id, $type, $newFilename, $montant_reel);
                $data = json_decode($rep->getContent(), true);

                if ($data['success']) {
                    $uploadedFiles[] = $newFilename;
                } else {
                    $errors[] = $data['message'];
                }
            } catch (\Exception $e) {
                $errors[] = 'Erreur lors du téléchargement du fichier ' . $file->getClientOriginalName() . ' : ' . $e->getMessage();
            }
        }
        if (empty($errors)) {
            return $this->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' fichier(s) téléchargé(s) avec succès',
                'files' => $uploadedFiles,
                'path' => $this->generateUrl('dm_piece.liste_demande')
            ]);
        } else {
            return $this->json([
                'success' => false,
                'message' => 'Des erreurs sont survenues lors du téléchargement',
                'errors' => $errors,
                'files' => $uploadedFiles
            ]);
        }
    }
}
