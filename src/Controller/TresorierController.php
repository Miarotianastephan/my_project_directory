<?php

namespace App\Controller;

use App\Entity\DemandeType;
use App\Entity\LogDemandeType;
use App\Entity\Utilisateur;
use App\Repository\DemandeTypeRepository;
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
            'demande_types' => $dm_Repository->findByEtat(30)
        ]);

    }

    #[Route('/demande/{id}', name: 'tresorier.detail_demande_en_attente', methods: ['GET'])]
    public function show($id,EntityManagerInterface $entityManager): Response
    {
        $data = $entityManager->find(DemandeType::class, $id);
        return $this->render('tresorier/show.html.twig', [ 'demande_type' => $data ]);
    }

    #[Route('/demande/valider/{id}', name: 'tresorier.valider_fond', methods: ['GET'])]
    public function valider_fond($id,EntityManagerInterface $entityManager): Response
    {
        $data = $entityManager->find(DemandeType::class, $id);
        return $this->render('tresorier/deblocker_fond.html.twig', ['demande_type' => $data]);
    }

    #[Route('/remettre_fond/{id}', name: 'tresorier.remettre_fond', methods: ['POST'])]
    public function remettre_fond($id,EntityManagerInterface $entityManager): JsonResponse
    {

        $id_user_tresorier = 3;
        $dm_type = $entityManager->find(DemandeType::class, $id);
        $user_tresorier = $entityManager->find(Utilisateur::class, $id_user_tresorier);
        $user_sg = $dm_type->getUtilisateur();

        $log_dm = new LogDemandeType();
        $log_dm->setDmEtat($dm_type->getDmEtat());
        $log_dm->setUserMatricule($user_sg->getUserMatricule());
        $log_dm->setDemandeType($dm_type);

        $script = "INSERT INTO log_demande_type (LOG_DM_ID, DEMANDE_TYPE_ID, LOG_DM_DATE, DM_ETAT, USER_MATRICULE) VALUES (log_etat_demande_seq.NEXTVAL,:dm_type_id,DEFAULT,:etat,:user_matricule)";

        try {
            $connection = $entityManager->getConnection();
            $connection->beginTransaction();
            $statement = $connection->prepare($script);
            $statement->bindValue('dm_type_id', $log_dm->getDemandeType()->getId());
            $statement->bindValue('etat', $log_dm->getDmEtat());
            $statement->bindValue('user_matricule', $log_dm->getUserMatricule());

            $statement->executeQuery();
            $connection->commit();

            // MAJ de dm_type la base de données
            $dm_type->setDmEtat(40);
            $dm_type->setUtilisateur($user_tresorier);
            $entityManager->persist($dm_type);
            $entityManager->flush();
        } catch (Exception $exception) {
            $connection->rollBack();
            throw $exception;
        }


        return new JsonResponse([
            'success' => true,
            'message' => 'La demande a été remis',
            'path' => $this->generateUrl('tresorier.liste_demande_en_attente')
        ]);
    }



}
