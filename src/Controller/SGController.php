<?php

namespace App\Controller;

use App\Entity\DemandeType;
use App\Entity\LogDemandeType;
use App\Entity\Utilisateur;
use App\Repository\DemandeTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route('/sg')]
class SGController extends AbstractController
{

    #[Route(path: '/', name: 'SG.liste_demande_en_attente', methods: ['GET'])]
    public function index(DemandeTypeRepository $dm_Repository): Response
    {
        return $this->render('sg/index.html.twig', [
            //'demande_types' => $dm_Repository->findAll()->where($dm_Repository == 10)
            'demande_types' => $dm_Repository->findByEtat(10)
        ]);
    }

    #[Route('/{id}', name: 'SG.detail_demande_en_attente', methods: ['GET'])]
    public function show($id, DemandeTypeRepository $dm_Repository): Response
    {
        $data = $dm_Repository->find($id);
        return $this->render('sg/show.html.twig', ['demande_type' => $data]);
    }

    #[Route('/demande/valider/{id}', name: 'SG.valider_en_attente', methods: ['GET'])]
    public function valider($id, DemandeTypeRepository $dm_Repository): Response
    {
        $data = $dm_Repository->find($id);
        return $this->render('sg/valider_demande.html.twig', ['demande_type' => $data]);
    }

    #[Route('/demande/refuser/{id}', name: 'SG.refus_demande_en_attente', methods: ['GET'])]
    public function refuser($id, DemandeTypeRepository $dm_Repository): Response
    {
        $data = $dm_Repository->find($id);
        return $this->render('sg/refuser_demande.html.twig', ['demande_type' => $data]);
    }

    #[Route('/valider_demande/{id}', name: 'valider_demande', methods: ['POST'])]
    public function valider_demande($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $id_user_sg = 2;

        $dm_type = $entityManager->find(DemandeType::class, $id);

        $user_sg = $entityManager->find(Utilisateur::class, $id_user_sg);
        $user_demande = $dm_type->getUtilisateur();

        $log_dm = new LogDemandeType();
        $log_dm->setDmEtat($dm_type->getDmEtat());
        $log_dm->setUserMatricule($user_demande->getUserMatricule());
        $log_dm->setDemandeType($dm_type);


        $script = "INSERT INTO log_demande_type (LOG_DM_ID, DEMANDE_TYPE_ID, LOG_DM_DATE, DM_ETAT, USER_MATRICULE) VALUES (log_etat_demande_seq.NEXTVAL,:dm_type_id,DEFAULT,:etat,:user_matricule)";


        $connection = $entityManager->getConnection();
        try {
            $connection->beginTransaction();
            $statement = $connection->prepare($script);
            $statement->bindValue('dm_type_id', $log_dm->getDemandeType()->getId());
            $statement->bindValue('etat', $log_dm->getDmEtat());
            $statement->bindValue('user_matricule', $log_dm->getUserMatricule());
            $statement->executeQuery();
            $connection->commit();

            // MAJ de dm_type la base de données
            $dm_type->setDmEtat(30);
            $dm_type->setUtilisateur($user_sg);
            //$entityManager->persist($dm_type);

            /**
             * Mila ovaine ilay date de demande
             *
             * */

            $entityManager->flush();
        } catch (Exception $exception) {
            $connection->rollBack();
            throw $exception;
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'La demande a été valider',
            'path' => $this->generateUrl('SG.liste_demande_en_attente')
        ]);
    }

    #[Route('/refuser_demande/{id}', name: 'refuser_demande', methods: ['POST', 'GET'])]
    public function refuser_demande($id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $id_user_sg = 2;

        $data = json_decode($request->getContent(), true);
        $commentaire_data = $data['commentaire'] ?? null;

        $dm_type = $entityManager->find(DemandeType::class, $id);

        $user_sg = $entityManager->find(Utilisateur::class, $id_user_sg);
        $user_demande = $dm_type->getUtilisateur();

        $log_dm = new LogDemandeType();
        $log_dm->setDmEtat($dm_type->getDmEtat());
        $log_dm->setUserMatricule($user_demande->getUserMatricule());
        $log_dm->setLogDmObservation($commentaire_data);
        $log_dm->setDemandeType($dm_type);

        $script = "INSERT INTO log_demande_type (LOG_DM_ID, DEMANDE_TYPE_ID, LOG_DM_DATE, DM_ETAT, LOG_DM_OBSERVATION, USER_MATRICULE) VALUES (log_etat_demande_seq.NEXTVAL,:dm_type_id,DEFAULT,:etat,:observation,:user_matricule)";

        if ($commentaire_data != null) {
            $connection = $entityManager->getConnection();
            $connection->beginTransaction();

            try {
                $statement = $connection->prepare($script);
                $statement->bindValue('dm_type_id', $log_dm->getDemandeType()->getId());
                $statement->bindValue('observation', $log_dm->getLogDmObservation());
                $statement->bindValue('etat', $log_dm->getDmEtat());
                $statement->bindValue('user_matricule', $log_dm->getUserMatricule());
                $statement->executeQuery();
                $connection->commit();

                // MAJ de dm_type la base de données
                $dm_type->setDmEtat(20);
                $dm_type->setUtilisateur($user_sg);
                $entityManager->persist($dm_type);

                /**
                 * Mila ovaine ilay date de demande
                 *
                 * */

                $entityManager->flush();
            } catch (Exception $exception) {
                $connection->rollBack();
                throw $exception;
            }
            return new JsonResponse([
                'success' => true,
                'message' => 'Commentaire reçu : ' . $commentaire_data,
                'path' => $this->generateUrl('SG.liste_demande_en_attente')
            ]);
        } else {
            //dump($commentaire_data . ' : est invalide');
            return new JsonResponse([
                'success' => true,
                'message' => 'Pas de commentaire reçu ',
                'path' => $this->generateUrl('SG.liste_demande_en_attente')
            ]);
        }
    }
}

