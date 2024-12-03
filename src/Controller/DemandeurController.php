<?php

namespace App\Controller;

use App\Repository\DemandeTypeRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Repository\ExerciceRepository;
use App\Repository\PlanCompteRepository;
use App\Service\CompteService;
use App\Service\DemandeTypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DemandeurController extends AbstractController
{

    private $plan_cpt_repo;

    public function __construct(PlanCompteRepository $planCptRepo)
    {
        $this->plan_cpt_repo = $planCptRepo;
    }

    /**
     * Page de liste des demandes de décaissement de fond avec l'état initié
     *
     * @param DemandeTypeService $dmService
     * @return Response
     */
    #[Route('/demandeur', name: 'demandeur.liste_demande')]
    public function index(DemandeTypeService $dmService): Response
    {
        // Lister mes demandes
        $mes_demandes = $dmService->findAllMyDemandeTypesInit();
        $mylogs = $dmService->findAllMyDemande();
        return $this->render('demandeur/demandeur.html.twig', [
            'demandes' => $mes_demandes,
            'logs_demandes' => $mylogs
        ]);
    }

    /**
     * Formulaire d'ajout de demande de décaissement de fonds.
     *
     * @return Response
     */
    #[Route('/demandeur/form', name: 'demandeur.nouveau_demande')]
    public function addNewDemandeForm(): Response
    {
        $data_compte_depense = $this->plan_cpt_repo->findCompteDepense();
        $data_entity = $this->plan_cpt_repo->findEntityCode();
        return $this->render('demandeur/demandeur_add.html.twig', [
            'data_compte_depense' => $data_compte_depense,
            'data_entity' => $data_entity
        ]);
    }

    /**
     * Ajout de la demande de décaissement de fonds dans la base donnée.
     *
     * @param Request $request
     * @param DemandeTypeService $dmService
     * @param ExerciceRepository $exoRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route(path: '/demandeur/add', name: 'demandeur.save_nouveau_demande', methods: ['POST'])]
    public function addNewDemandeFormAction(Request $request, DemandeTypeService $dmService, ExerciceRepository $exoRepository)
    {
        // getExerciceId 
        $exercice = $exoRepository->getExerciceValide();
        $data_parametre = $request->request->all();

        // les données :
        $plan_cpt_entity_id = $data_parametre['id_plan_comptable_entity'];
        $plan_cpt_motif_id = $data_parametre['id_plan_comptable_motif'];
        $montant_demande = $data_parametre['dm_montant'];
        $paiement = $data_parametre['mode_paiement'];

        // les dates :
        $date_operation = $data_parametre['date_operation'];
        $date_saisie = $data_parametre['date_saisie'];

        $response_data = $dmService->addDemandeFonds($exercice, $plan_cpt_entity_id, $plan_cpt_motif_id, $montant_demande, $paiement, $date_saisie, $date_operation);
        dump($response_data);
        return $this->redirectToRoute('demandeur.liste_demande');
    }

    // get by ID

    /**
     * Page de détails demande de décaissement de fonds ave les PJ associé.
     *
     * @param $id
     * @param DemandeTypeRepository $dm_Repository
     * @param DetailDemandePieceRepository $demandePieceRepository
     * @return Response
     */
    #[Route('/demandeur/{id}', name: 'demandeur.detail_demande_en_attente', methods: ['GET'])]
    public function show($id, DemandeTypeRepository $dm_Repository, DetailDemandePieceRepository $demandePieceRepository): Response
    {
        $data = $dm_Repository->find($id);
        $list_img = $demandePieceRepository->findByDemandeType($data);
        dump(count($list_img));
        return $this->render('/demandeur/demandeur_show.html.twig',
            ['demande_type' => $data, 'images' => $list_img]
        );
    }

    // Avoir le plan compte
    #[Route(path: '/demandeur/find/all', name: 'demandeur.findplan', methods: ['GET'])]
    public function findAllCompteDepense(PlanCompteRepository $planCompteRepo, SerializerInterface $serializer)
    {
        $data_mere = $planCompteRepo->findCompteDepense();
        $jsonContent = $serializer->serialize($data_mere, 'json');
        return new JsonResponse($jsonContent, 200, [], true);
    }

    /**
     * Sauvegarde des modifications de demande de décaissement de fonds dans la base de donnée.
     *
     * @param Request $request
     * @param DemandeTypeService $demandeTypeService
     * @return JsonResponse
     */
    #[Route(path: 'demandeur/update/save', name: 'demandeur.update.save', methods: ['POST'])]
    public function updateDemandeFonds(Request $request, DemandeTypeService $demandeTypeService)
    {
        $id_demande_fonds = $request->request->get('id_demande_fonds');
        // $demande_date = $request->request->get('demande_date');
        $demande_montant_nouveau = $request->request->get('demande_montant_nouveau');
        $id_compte_depense = $request->request->get('id_compte_depense');
        $response_data = $demandeTypeService->updateDemandeFonds($id_demande_fonds, $demande_montant_nouveau, $id_compte_depense);
        dump($response_data);
        return new JsonResponse($response_data);
    }

}
