<?php

namespace App\Controller;

use App\Repository\CompteMereRepository;
use App\Repository\PlanCompteRepository;
use App\Service\PlanCompteService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/plan')]
class PlanCompteController extends AbstractController
{

    /**
     * Page de liste de tous les plans de compte.
     *
     * @param PlanCompteRepository $planCompte
     * @return Response
     */
    #[Route('/compte', name: 'app_plan_compte')]
    public function index(PlanCompteRepository $planCompte): Response
    {
        $data_plan_compte = $planCompte->findAll();
        return $this->render('plan_compte/plan_compte.html.twig',[
            'data_plan_compte' => $data_plan_compte,
        ]);
    }

    /**
     * @return Response
     */
    #[Route(path: '/import', name: 'app_plan_compte.import', methods: ['GET'])]
    public function importPlanCompte(){
        return $this->render('plan_compte/plan_compte_import.html.twig');
    }

    /**
     * Page d'import de plan de compte.
     * Il n'est possible 'importer le plan de compte qu'une seule fois.
     * Ce controller n'est accessible que s'il n'y a pas encore de plan de compte.
     *
     * @return Response
     */
    #[Route(path: '/add', name: 'app_plan_compte.add', methods: ['GET'])]
    public function ajouterPlanCompte(PlanCompteService $planCompteService, PlanCompteRepository $planCompteRepo){
        $has_data_plan_compte = $planCompteService->hasDataPlanCompte($planCompteRepo); // sécurisation du page si le plan compte non vide
        if($has_data_plan_compte == false){
            return $this->render('plan_compte/plan_compte_add.html.twig');
        }
        $this->addFlash('message', 'Page ajout plan compte non accessible.');
        return $this->redirectToRoute('app_plan_compte');
    }

    /**
     * Sauvegarde des plans de compte importer.
     *
     * @param Request $request
     * @param PlanCompteService $planCompteService
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route(path: '/import/save', name: 'app_plan_compte.save', methods: ['POST'])]
    public function sauvegarderImportPlanCompte(Request $request, PlanCompteService $planCompteService,EntityManagerInterface $entityManager){
        $data = json_decode($request->getContent(), true);
        foreach($data as $key => $compte){
            $planCompteService->insertHierarchy($entityManager, $compte);
        }
        return new JsonResponse([
            'status' => true,
            'message' => 'Importation réussie',
            'path' => $this->generateUrl('app_plan_compte'),
        ]);
    }

    /**
     * Sauvegarde des modifications apportées pour un plan de compte.
     *
     * @param Request $request
     * @param PlanCompteService $plService
     * @param PlanCompteRepository $plCptRepo
     * @param CompteMereRepository $cptMere
     * @return JsonResponse
     */
    #[Route(path: '/update/save', name: 'app_plan_compte.update.save', methods: ['POST'])]
    public function updatePlanCompte(Request $request, PlanCompteService $plService, PlanCompteRepository $plCptRepo, CompteMereRepository $cptMere){
        $plan_cpt_id = $request->request->get('plan_cpt_id');
        // find plan with the Id 
        $cpt_numero = $request->request->get('cpt_numero');
        $cpt_libelle = $request->request->get('cpt_libelle_old');
        $cpt_mere_numero = $request->request->get('compte_mere');
        $response_data = $plService->updatePlanCompte($plan_cpt_id, $cpt_numero, $cpt_libelle, $cpt_mere_numero,$plCptRepo, $cptMere);
        return new JsonResponse($response_data);
    }

    /**
     * Controller pour afficher la liste de tous les plans de compte
     *
     * @param CompteMereRepository $cptMereRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route(path: '/mere/findAll', name: 'app_plan_compte.findall', methods: ['GET'])]
    public function findAllCptMere(CompteMereRepository $cptMereRepository,  SerializerInterface $serializer){
        $data_mere = $cptMereRepository->findAll();
        $jsonContent = $serializer->serialize($data_mere, 'json');
        return new JsonResponse($jsonContent, 200, [], true);
    }


}
