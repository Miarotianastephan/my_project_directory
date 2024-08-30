<?php

namespace App\Controller;

use App\Entity\DemandeType;
use App\Entity\DetailDemandePiece;
use App\Form\DetailDemandePieceType;
use App\Repository\DemandeTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

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
    public function new($id, Request $request, DemandeTypeRepository $dm_rep, EntityManagerInterface $entityManager): Response
    {
        //$parameters = $request->request->all();
        $dm_type = $dm_rep->find($id);
        $type = $request->request->get('type');
        $file = $request->files->get('image');

        if ($file) {
            // Obtenir le nom de fichier original
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // Générer un nom unique pour éviter les conflits
            $newFilename = uniqid() . '.' . $file->guessExtension();

            // Définir le répertoire de destination pour le fichier téléchargé
            // Assurez-vous que le paramètre 'uploads_directory' est défini dans config/services.yaml
            $destination = $this->getParameter('uploads_directory');
            $connection = $entityManager->getConnection();
            $connection->beginTransaction();
            try {

                // Déplacer le fichier dans le répertoire de destination
                $file->move($destination, $newFilename);
                $detail_dm = new DetailDemandePiece();
                $detail_dm->setDemandeType($dm_type);
                $detail_dm->setDetDmTypeUrl($type);
                $detail_dm->setDetDmPieceUrl($newFilename);

                $script = "INSERT INTO log_etat_demande (DETAIL_DM_TYPE_ID, DEMANDE_TYPE_ID, DET_DM_PIECE_URL, DET_DM_TYPE_URL, DET_DM_DATE) VALUES (DETAIL_DM_TYPE_SEQ.NEXTVAL,:dm_type_id,:det_dm_piece_url,:det_dm_type_url,DEFAULT)";

                $statement = $connection->prepare($script);
                $statement->bindValue('dm_type_id', null);
                $statement->bindValue('det_dm_piece_url', null);
                $statement->bindValue('det_dm_type_url', null);
                $statement->executeQuery();
                $connection->commit();

                //dump("nom = ".$destination);
                // Message de confirmation
                $file->move($destination, $newFilename);
                return new Response('Fichier téléchargé avec succès : ' . $newFilename);
            } catch (Exception $e) {
                $connection->rollBack();
                // Gestion de l'erreur si le fichier ne peut pas être déplacé
                return new Response('Erreur lors du téléchargement du fichier : ' . $e->getMessage());
            }
        }

        return new Response('Aucun fichier téléchargé');
    }
}
