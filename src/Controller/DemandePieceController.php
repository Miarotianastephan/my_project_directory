<?php

namespace App\Controller;

use App\Repository\DemandeTypeRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Service\DemandeTypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $data = $dm_rep->findByEtat(301);
        return $this->render('demande_piece/ajout_piece_justificative.html.twig', [
            'demande_types' => $data
        ]);
    }

    #[Route(path: '/dm/{id}', name: 'dm_piece.show', methods: ['GET'])]
    public function dm_piece($id, DemandeTypeRepository $dm_rep): Response
    {
        // Vérifier si l'ID est un nombre
        if (is_numeric($id)) {
            $data = $dm_rep->find($id);
            if (!$data) {
                throw new NotFoundHttpException('Demande non trouvée');
            }
            return $this->render('demande_piece/_form_pj.html.twig', [
                'demande_type' => $data
            ]);
        }
        return $this->render("exercice/index.html.twig");
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
                if ($fileSizeInBytes > $this->megaByte) {
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
